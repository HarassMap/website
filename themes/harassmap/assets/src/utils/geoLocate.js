'use strict';

import _ from 'lodash';
import moment from 'moment';
import MapFactory from "../map/map.factory";
import Handlebars from "handlebars";
import { CENTER_MAP, emitter } from './events';

export const initGeolocate = () => {
    initAddressListener();
    initItJustHappenedHere();
    initItHappenedElsewhere();
    initMarkerListener();
};

const initMarkerListener = () => {
    let $moved = $('#marker-moved');
    let source = $("#alert-template").html();
    let template = Handlebars.compile(source);

    $('#report').on('submit', (event) => {
        let moved = $moved.val();

        if (moved !== 'true') {
            event.preventDefault();

            $moved.val(true);

            $('.alerts').html(template());
        }
    });
};

const initAddressListener = () => {
    let $address = $('#address'),
        $city = $('#city'),
        valueCache = '';

    $('#region, #city, #address').on('blur', () => {
        let value = $address.val() + ', ' + $city.val();

        // if the value has changed
        if (value !== valueCache) {
            let geocoder = new google.maps.Geocoder;

            // cache the value
            valueCache = value;

            geocoder.geocode({address: value}, (results, status) => {
                if (status === 'OK' && !_.isEmpty(results)) {
                    let result = results[0],
                        location = result.geometry.location.toJSON();

                    setPosition(location);
                }
            });
        }
    });
};

const initItJustHappenedHere = () => {
    let $geolocate = $('#geolocate');
    let $elsewhere = $('#elsewhere');
    let $geoText = $('.instructions--geolocate');
    let $elsText = $('.instructions--elsewhere');

    $geolocate.on('click', (event) => {
        event.preventDefault();

        $('#marker-moved').val(true);

        $elsewhere.removeClass('active');
        $geolocate.addClass('active');
        $geoText.show();
        $elsText.hide();

        if (!navigator || !navigator.geolocation) {
            alert('cannot get location');
        }

        navigator.geolocation.getCurrentPosition((position) => {
            let geocoder = new google.maps.Geocoder,
                location = {
                    lat: position.coords.latitude,
                    lng: position.coords.longitude
                };

            setPosition(location);

            $('#date').val(moment().format('YYYY-MM-DD'));
            $('#time').val(moment().format('h:mma'));

            geocoder.geocode({location: location}, (results, status) => {
                if (status === 'OK' && !_.isEmpty(results)) {
                    let result = results[0],
                        address = result.formatted_address,
                        parts = _.split(address, ','),
                        chunks = _.chunk(parts, Math.ceil(parts.length / 2));

                    // set the values for the address and trigger the change
                    $('#address').val(_.join(chunks[0], ', ')).trigger('change');
                    $('#city').val(_.join(chunks[1], ', ')).trigger('change');


                } else {
                    window.alert('Geocoder failed due to: ' + status);
                }
            });
        });
    });
};

const initItHappenedElsewhere = () => {
    let $geolocate = $('#geolocate');
    let $elsewhere = $('#elsewhere');
    let $geoText = $('.instructions--geolocate');
    let $elsText = $('.instructions--elsewhere');

    $elsewhere.on('click', (event) => {
        event.preventDefault();

        $geolocate.removeClass('active');
        $elsewhere.addClass('active');

        $geoText.hide();
        $elsText.show();

        $('#address, #city, #date, #time').val('');
    });
};

const setPosition = (position) => {
    let $lat = $('#lat'),
        $lng = $('#lng');

    emitter.emit(CENTER_MAP, position);

    $lat.val(position.lat.toFixed(5));
    $lng.val(position.lng.toFixed(5));
};
'use strict';

import Handlebars from "handlebars";
import _ from 'lodash';
import moment from 'moment-timezone';
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

            let time = moment().tz($('#timezone').val());

            $('#date').val(time.format('YYYY-MM-DD'));
            $('#time').val(time.format('h:mma'));

            geocoder.geocode({location: location}, (results, status) => {
                if (status === 'OK' && !_.isEmpty(results)) {
                    let result = results[0],
                        addressTypes = ['street_number', 'route'],
                        cityTypes = ['locality', 'administrative_area_level_1', 'country'],
                        address = [],
                        city = [];

                    // build the address from the address components
                    _.forEach(result.address_components, (component) => {
                        if (_.some(addressTypes, (value) => _.indexOf(component.types, value) !== -1)) {
                            address.push(component.long_name);
                        } else if (_.some(cityTypes, (value) => _.indexOf(component.types, value) !== -1)) {
                            city.push(component.long_name);
                        }
                    });

                    // set the values for the address and trigger the change
                    $('#address').val(_.join(address, ', ')).trigger('change');
                    $('#city').val(_.join(city, ', ')).trigger('change');


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
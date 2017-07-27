'use strict';

import _ from 'lodash';
import moment from 'moment';
import MapFactory from "../map/map.factory";

export const initGeolocate = () => {
    initAddressListener();
    initItJustHappenedHere();
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
                    let result = results[0];

                    let location = result.geometry.location.toJSON();

                    setPosition(location);
                }
            });
        }
    });
};

const initItJustHappenedHere = () => {
    $('#geolocate').on('click', (event) => {
        event.preventDefault();

        alert('clicked on this');

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

const setPosition = (position) => {
    let map = MapFactory.getMap(),
        $lat = $('#lat'),
        $lng = $('#lng');

    map.setCenter(position);

    $lat.val(position.lat.toFixed(5));
    $lng.val(position.lng.toFixed(5));
};
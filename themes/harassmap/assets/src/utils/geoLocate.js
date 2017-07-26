'use strict';

import _ from 'lodash';
import MapFactory from "../map/map.factory";

export const initGeolocate = () => {
    initAddressListener();
    initItJustHappenedHere();
};

const initAddressListener = () => {
    let $address = $('#address'),
        $city = $('#city'),
        $region = $('#region'),
        $lat = $('#lat'),
        $lng = $('#lng'),
        valueCache = '';

    $('#region, #city, #address').on('blur', () => {
        let value = $address.val() + ', ' + $city.val() + ', ' + $region.val();

        // if the value has changed
        if (value !== valueCache) {

            // cache the value
            valueCache = value;

            let map = MapFactory.getMap();
            let geocoder = new google.maps.Geocoder;

            geocoder.geocode({address: value}, (results, status) => {
                let result = results[0];

                if (result) {
                    let location = result.geometry.location.toJSON();

                    setPosition(location);
                }
            });
        }
    });
};

const initItJustHappenedHere = () => {
    $('#geolocate').on('click', (event) => {
        let map = MapFactory.getMap(),
            geocoder = new google.maps.Geocoder;

        navigator.geolocation.getCurrentPosition((position) => {
            let location = {
                lat: position.coords.latitude,
                lng: position.coords.longitude
            };

            setPosition(location);

            geocoder.geocode({location: location}, (results, status) => {
                if (status === 'OK') {

                    // make sure we have address results
                    if (!_.isEmpty(results)) {
                        let result = results[0];
                    }

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
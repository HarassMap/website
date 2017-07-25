'use strict';

import MapFactory from "../map/map.factory";

export const initGeolocate = () => {
    initAddressListener();
    initItJustHappenedHere();
};

const initAddressListener = () => {
    let $address = $('#address');
    let $city = $('#city');
    let $region = $('#region');
    let valueCache = '';

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

                if(result) {
                    let location = result.geometry.location.toJSON();

                    map.setCenter(location);
                }
            });
        }
    });
};

const initItJustHappenedHere = () => {
    $('#geolocate').on('click', (event) => {
        let map = MapFactory.getMap();
        let geocoder = new google.maps.Geocoder;

        navigator.geolocation.getCurrentPosition((position) => {
            let pos = {
                lat: position.coords.latitude,
                lng: position.coords.longitude
            };

            map.setCenter(pos);

            geocoder.geocode({location: pos}, (results, status) => {
                if (status === 'OK') {
                    if (results[1]) {

                    } else {
                        window.alert('No results found');
                    }
                } else {
                    window.alert('Geocoder failed due to: ' + status);
                }
            });
        });
    });
}
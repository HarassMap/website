'use strict';

import MapFactory from "../map/map.factory";

export const initGeolocate = (id, map) => {
    document.getElementById(id).addEventListener('click', (event) => {
        let map = MapFactory.getMap();
        let geocoder = new google.maps.Geocoder;

        navigator.geolocation.getCurrentPosition((position) => {
            let pos = {
                lat: position.coords.latitude,
                lng: position.coords.longitude
            };

            map.setCenter(pos);

            geocoder.geocode({'location': pos}, (results, status) => {
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
};
'use strict';

import _ from "lodash";
import Pikaday from "pikaday";
import MapFactory from "./map/map.factory";

let map = null;

window.initMap = (id = 'map') => {
    map = MapFactory.createFromElement(document.getElementById(id));
};

window.createDatePicker = (id) => {
    let picker = new Pikaday({field: document.getElementById(id)});
};

window.createTimePicker = (id) => {
    $('#' + id).timepicker();
};

window.initAutoComplete = (id, data) => {
    const cities = new Bloodhound({
        datumTokenizer: Bloodhound.tokenizers.obj.whitespace('name'),
        queryTokenizer: Bloodhound.tokenizers.whitespace,
        identify: ({name}) => name,
        local: data
    });

    const listener = (event, data) => {
        if (_.isString(data)) {
            data = cities.get(data)[0];
        }

        // if we have a map and data
        if (map && data) {
            map.setCenter(new google.maps.LatLng(data.lat, data.lon));
        }

    };

    $('#' + id)
        .typeahead({highlight: true, hint: false}, {
            name: 'cities',
            display: 'name',
            source: cities
        })
        .bind('typeahead:change', listener)
        .bind('typeahead:select', listener)
        .bind('typeahead:autocomplete', listener);
};

window.initGeolocate = (id) => {
    document.getElementById(id).addEventListener('click', (event) => {
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
                        console.debug(results);
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
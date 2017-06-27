'use strict';

import _ from "lodash";

export const initAutoComplete = (id, data) => {
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
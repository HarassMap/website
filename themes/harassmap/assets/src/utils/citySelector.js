'use strict';

import _ from "lodash";
import { CENTER_MAP, emitter } from './events';

export const initCitySelector = () => {
    let $country = $('#country');

    $country.on('changed', () => {
        onCountryChange();
    });

    onCountryChange();
};

const onCountryChange = () => {
    let $country = $('#country'),
        country = $country.val();

    $.request('onCountrySelect', {
        data: {
            country
        },
        success: function (data) {
            initAutocomplete(data);
        }
    });
};

const initAutocomplete = (data) => {
    let $city = $('#city');

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
        if (data) {
            emitter.emit(CENTER_MAP, new google.maps.LatLng(data.lat, data.lng));
        }

    };

    $city
        .typeahead({
            highlight: true,
            hint: false
        }, {
            name: 'cities',
            display: 'name',
            source: cities
        })
        .bind('typeahead:change', listener)
        .bind('typeahead:select', listener)
        .bind('typeahead:autocomplete', listener);
};
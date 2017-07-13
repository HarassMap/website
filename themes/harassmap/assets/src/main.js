'use strict';

import MapFactory from "./map/map.factory";
import { initCitySelector } from "./utils/citySelector";

import { createDatePicker } from "./utils/datePicker";
import { initGeolocate } from "./utils/geoLocate";
import { createTimePicker } from "./utils/timePicker";
import { initToggleIntervention } from "./utils/toggleIntervention";

window.initMap = (id = 'map') => {
    MapFactory.createFromElement(document.getElementById(id));
};

window.initReportIncidentPage = () => {
    createDatePicker('date');
    createTimePicker('time');
    initCitySelector();
    initGeolocate('geolocate');
    initToggleIntervention();
};

$('.row-link').each((index, element) => {
    let href = element.dataset.href;

    if (href) {
        $(element).on('click', () => {
            window.location = href;
        });
    }
});
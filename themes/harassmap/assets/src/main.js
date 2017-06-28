'use strict';

import MapFactory from "./map/map.factory";

import { createDatePicker } from "./utils/datePicker";
import { initGeolocate } from "./utils/geoLocate";
import { createTimePicker } from "./utils/timePicker";
import { initToggleIntervention } from "./utils/toggleIntervention";
import { initCitySelector } from "./utils/citySelector";

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
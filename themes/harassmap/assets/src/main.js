'use strict';

import MapFactory from "./map/map.factory";

import { createDatePicker } from "./utils/datePicker";
import { initGeolocate } from "./utils/geoLocate";
import { createTimePicker } from "./utils/timePicker";

window.initMap = (id = 'map') => {
    MapFactory.createFromElement(document.getElementById(id));
};

window.initReportIncidentPage = () => {
    createDatePicker('date');
    createTimePicker('time');
    // initAutoComplete('incident_report_location_city', {{ cities | serialize('json') | raw }});
    initGeolocate('geolocate');

    // show/hide the assistance
    let $assistance = $('.assistance'),
        $intervention = $('#intervention');

    // hide the assistance to begin with
    $assistance.hide();

    $intervention.on('change', () => {
        let value = $intervention.val();

        if (value === "1") {
            $assistance.slideDown();
        } else {
            console.debug("slide up?");
            $assistance.slideUp();
        }

    });

};
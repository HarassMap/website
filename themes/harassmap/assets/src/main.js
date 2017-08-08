'use strict';

import MapFactory from "./map/map.factory";
import { initBanner } from './utils/banner';

import { createDatePicker } from "./utils/datePicker";
import { initGeolocate } from "./utils/geoLocate";
import { initMenu } from './utils/menu';
import { createTimePicker } from "./utils/timePicker";
import { initToggleIntervention } from "./utils/toggleIntervention";

$('body').addClass('js');

initMenu();

// enable all tooltips
$('[data-toggle="tooltip"]').tooltip();

window.initMap = () => {
    $('.map').each(function () {
        MapFactory.createFromElement(this);
    });
};

window.initBanner = initBanner;

window.initReportIncidentPage = () => {
    createDatePicker('date');
    createTimePicker('time');
    initGeolocate();
    initToggleIntervention();
    $('#timezone').select2({width: '100%'});
};

$('.row-link').each((index, element) => {
    let href = element.dataset.href;

    if (href) {
        $(element).on('click', () => {
            window.location = href;
        });
    }
});

$('.floating').addClass('js-float-label-wrapper');
$('.js-float-label-wrapper').FloatLabel();
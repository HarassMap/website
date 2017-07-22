'use strict';

import MapFactory from "./map/map.factory";
import { initCitySelector } from "./utils/citySelector";

import { createDatePicker } from "./utils/datePicker";
import { emitter, REFRESH_MAP, FILTER_MAP } from './utils/events';
import { initGeolocate } from "./utils/geoLocate";
import { createTimePicker } from "./utils/timePicker";
import { initToggleIntervention } from "./utils/toggleIntervention";

// enable all tooltips
$('[data-toggle="tooltip"]').tooltip();

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

window.initBanner = () => {
    let $banners = $('.banner'),
        $controls = $('.control');

    $banners.not('.active').hide();

    $controls.on('click', function (event) {
        let $control = $(this),
            href = $control.attr('href');

        event.preventDefault();

        $controls.removeClass('active');
        $control.addClass('active');

        $banners.hide();
        $(href).show();

        emitter.emit(REFRESH_MAP);
    });
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

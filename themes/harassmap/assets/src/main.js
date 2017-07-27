'use strict';

import MapFactory from "./map/map.factory";
import { initBanner } from './utils/banner';

import { createDatePicker } from "./utils/datePicker";
import { initGeolocate } from "./utils/geoLocate";
import { createTimePicker } from "./utils/timePicker";
import { initToggleIntervention } from "./utils/toggleIntervention";

$('body').addClass('js');

// enable all tooltips
$('[data-toggle="tooltip"]').tooltip();

window.initMap = (id = 'map') => {
    MapFactory.createFromElement(document.getElementById(id));
};

window.initBanner = initBanner;

window.initReportIncidentPage = () => {
    createDatePicker('date');
    createTimePicker('time');
    initGeolocate();
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

$('.floating').addClass('js-float-label-wrapper');
$('.js-float-label-wrapper').FloatLabel();

$('.navbar-toggler').on('click', () => {
    let $navbar = $('.navbar'),
        $nav = $('#main_nav');

    if ($navbar.hasClass('open')) {
        $navbar.removeClass('open');
        $nav.slideUp();
    } else {
        $navbar.addClass('open');
        $nav.slideDown();
    }
});
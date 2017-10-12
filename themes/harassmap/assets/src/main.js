'use strict';

import moment from "moment";
import { initCircleChart, initHomeChart, initLineChart } from './chart/charts';
import { changeD3Locale } from "./locale/d3";
import "./locale/moment";
import MapFactory from "./map/map.factory";
import { initBanner } from './utils/banner';
import { createDatePicker } from "./utils/datePicker";
import { initGeolocate } from "./utils/geoLocate";
import { initMenu } from './utils/menu';
import { createTimePicker } from "./utils/timePicker";
import { initToggleIntervention } from "./utils/toggleIntervention";

$(document).ready(() => {
    $('body').addClass('js');

    initMenu();

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

    // enable all tooltips
    $('[data-toggle="tooltip"]').tooltip();

    $('.match-height').matchHeight();
});

window.setLocale = (locale) => {
    moment.locale(locale);
    changeD3Locale(locale);
};

window.initMap = () => {
    $('.map').each(function () {
        MapFactory.createFromElement(this);
    });
};

window.initHomeChart = initHomeChart;
window.initCircleChart = initCircleChart;
window.initLineChart = initLineChart;

window.initReportBanner = () => {
    initBanner();
};

window.initReportIncidentPage = () => {
    createDatePicker('date');
    createTimePicker('time');
    initGeolocate();
    initToggleIntervention();
    $('#timezone').select2({width: '100%'});
};

window.initReportTablePage = () => {
    createDatePicker('date_from');
    createDatePicker('date_to');
};

window.initShareButtons = (shareUrl) => {
    (function (d, s, id) {
        var js, fjs = d.getElementsByTagName(s)[0];
        if (d.getElementById(id)) {
            return;
        }
        js = d.createElement(s);
        js.id = id;
        js.src = "//connect.facebook.net/en_US/sdk.js";
        fjs.parentNode.insertBefore(js, fjs);
    }(document, 'script', 'facebook-jssdk'));

    $('#twitter-share').on('click', function (event) {
        event.preventDefault();

        window.open(this.href, 'popup', 'width=600,height=255');
    });

    $('#facebook-share').on('click', function (event) {
        event.preventDefault();

        // make sure we have the global FB variable
        if (window.FB) {
            FB.ui({
                method: 'share',
                href: shareUrl
            }, function (response) {

            });
        }
    });
};
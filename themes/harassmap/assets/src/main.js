'use strict';

import moment from "moment";
import { initHomeChart } from './chart/home';
import "./locale/moment";
import MapFactory from "./map/map.factory";
import { initBanner } from './utils/banner';
import { createDatePicker } from "./utils/datePicker";
import { stopMultipleSubmits } from './utils/form';
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
};

window.initMap = () => {
    $('.map').each(function () {
        MapFactory.createFromElement(this);
    });
};

window.initReportBanner = () => {
    initBanner();
    initHomeChart();
};

window.initReportIncidentPage = () => {
    createDatePicker('date');
    createTimePicker('time');
    initGeolocate();
    initToggleIntervention();
    stopMultipleSubmits('report');
    $('#timezone').select2({width: '100%'});
};

window.initReportTablePage = () => {
    createDatePicker('date_from');
    createDatePicker('date_to');
};

// facebook
window.fbAsyncInit = () => {
    FB.init({
        appId: '{{ domain.facebook_app_id }}',
        autoLogAppEvents: true,
        xfbml: true,
        version: 'v2.9'
    });
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
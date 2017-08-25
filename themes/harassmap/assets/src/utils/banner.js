'use strict';

import screenfull from 'screenfull';
import { emitter, REFRESH_MAP, FILTER_MAP } from './events';
import { createDatePicker } from "./datePicker";

export const initBanner = () => {
    initBannerSwitcher();
    initFullscreenMap();
    initMapFilter();
};

const initBannerSwitcher = () => {
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

const initFullscreenMap = () => {
    let $map = $('#report-map');

    alert("initFullscreenMap");

    screenfull.on('change', () => {

        if (screenfull.isFullscreen) {
            $map.addClass('full-screen');
        } else {
            $map.removeClass('full-screen');
        }
    });

    $('#map-fullscreen').on('click', function (event) {
        alert("on click");

        event.preventDefault();

        if (screenfull.enabled) {
            screenfull.toggle($map[0]);
        }
    });
};

const initMapFilter = () => {
    let $filterButton = $('#map-filter'),
        $filter = $('#filter'),
        $filterForm = $('#filter-form');

    createDatePicker('date_from');
    createDatePicker('date_to');

    $filterButton.on('click', function (event) {
        event.preventDefault();

        $filter.fadeToggle();
        $filterButton.toggleClass('active');
    });

    $filterForm.on('submit', function (event) {
        event.preventDefault();

        let data = $filterForm.serializeJSON();

        emitter.emit(FILTER_MAP, data);
    });
};
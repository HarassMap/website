'use strict';

import screenfull from 'screenfull';
import { emitter, REFRESH_MAP } from './events';

export const initBanner = () => {
    initFullscreenMap();
    initBannerSwitcher();
    // initMapFilter();
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

    screenfull.on('change', () => {

        if (screenfull.isFullscreen) {
            $map.addClass('full-screen');
        } else {
            $map.removeClass('full-screen');
        }
    });

    $('#map-fullscreen').on('click', (event) => {
        event.preventDefault();

        if (screenfull.enabled) {
            screenfull.toggle($map[0]);
        }
    });
};

const initMapFilter = () => {
    let $filterButton = $('#map-filter'),
        $filter = $('#filter');

    $filterButton.on('click', (event) => {
        event.preventDefault();

        $filter.toggle();
        $filterButton.toggleClass('active');
    });
};
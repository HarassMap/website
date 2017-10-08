'use strict';

import screenfull from 'screenfull';
import { createDatePicker } from "./datePicker";
import { emitter, FILTER_MAP } from './events';

export const initMapFilter = () => {
    let $filterButton = $('#mapFilter'),
        $filter = $('#filter'),
        $filterForm = $('#filterForm');

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

export const initFullscreenMap = () => {
    let $map = $('#reportMap'),
        $button = $('#mapFullscreen');

    if (screenfull.enabled) {
        $button.on('click', function (event) {
            event.preventDefault();

            screenfull.toggle($map[0]);
        });

        screenfull.on('change', () => {

            if (screenfull.isFullscreen) {
                $map.addClass('full-screen');
            } else {
                $map.removeClass('full-screen');
            }
        });

    } else {
        $button.hide();
    }
};
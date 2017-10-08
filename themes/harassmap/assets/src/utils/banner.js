'use strict';

import { BANNER_SWITCH, emitter, REFRESH_MAP } from './events';

export const initBanner = () => {
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
        emitter.emit(BANNER_SWITCH);
    });
};
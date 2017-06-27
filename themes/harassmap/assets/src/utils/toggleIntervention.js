'use strict';

export const initToggleIntervention = () => {
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
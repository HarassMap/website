'use strict';

const checkAssistance = () => {
    let $assistance = $('.assistance'),
        $intervention = $('#intervention');

    let value = $intervention.val();

    if (value === "1") {
        $assistance.slideDown();
    } else {
        $assistance.slideUp();
    }
};

export const initToggleIntervention = () => {
    let $assistance = $('.assistance'),
        $intervention = $('#intervention');

    // hide the assistance to begin with
    $assistance.hide();

    checkAssistance();

    $intervention.on('change', () => checkAssistance());
};
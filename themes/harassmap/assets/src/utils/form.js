'use strict';

export const stopMultipleSubmits = (id) => {
    let submitted = false;

    $('#' + id).on('submit', (event) => {
        if (submitted) {
            event.preventDefault();
            console.debug("stopping");
        }

        submitted = true;
    });
};
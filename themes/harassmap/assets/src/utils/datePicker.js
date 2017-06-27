'use strict';

import Pikaday from "pikaday";

export const createDatePicker = (id) => {
    let picker = new Pikaday({field: document.getElementById(id)});
};
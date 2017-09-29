'use strict';

import moment from "moment";
import Pikaday from "pikaday";

export const createDatePicker = (id) => {
    let picker = new Pikaday({
        field: document.getElementById(id),
        i18n: {
            months: moment.localeData()._months,
            weekdays: moment.localeData()._weekdays,
            weekdaysShort: moment.localeData()._weekdaysShort
        }
    });
};
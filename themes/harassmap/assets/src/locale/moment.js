'use strict';

import moment from "moment";
import "moment/min/locales";

const arMonths = [
    'يناير',
    'فبراير',
    'مارس',
    'ابريل',
    'مايو',
    'يونيو',
    'يوليو',
    'اغسطس',
    'سبتمبر',
    'اكتوبر',
    'نوفمبر',
    'ديسمبر',
];

const enData = moment().locale('en').localeData();

moment.updateLocale('ar', {
    months : arMonths,
    monthsShort : arMonths,
    postformat: enData.postformat,
    meridiem: enData.meridiem
});
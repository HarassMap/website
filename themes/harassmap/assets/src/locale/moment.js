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

moment.updateLocale('ar', {
    months : arMonths,
    monthsShort : arMonths,
});
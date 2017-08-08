'use strict';

import { HomePageMap } from "./map.home";
import { ReportPageMap } from "./map.report";
import { ReportsPageMap } from "./map.reports";

export default {

    createFromElement(element) {
        return this.create(element, element.dataset.map);
    },

    create(element, type = 'homepage') {
        let map = null;

        switch (type) {
            case 'homepage':
                map = new HomePageMap(element);
                break;
            case 'report':
                map = new ReportPageMap(element);
                break;
            case 'reports':
                map = new ReportsPageMap(element);
                break;
        }

        return map
    }

};
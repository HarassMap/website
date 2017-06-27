'use strict';

import { HomePageMap } from "./map.home";
import { ReportPageMap } from "./map.report";

let map = null;

export default {

    getMap() {
        return map;
    },

    createFromElement(element) {
        return this.create(element, element.dataset.map);
    },

    create(element, type = 'homepage') {
        switch (type) {
            case 'homepage':
                map = new HomePageMap(element);
                break;
            case 'report':
                map = new ReportPageMap(element);
                break;
        }

        return map
    }

};
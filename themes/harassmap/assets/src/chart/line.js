'use strict';

import * as d3 from 'd3';
import debounce from 'debounce';
import Handlebars from "handlebars";

export class LineChart {

    constructor(element) {
        this.svg = d3.select(element);
        this.ready = false;
        this.data = JSON.parse(element.dataset.items);

        this.addListeners();

        this.parseData();
    }

    /**
     * Listen to resize events
     */
    addListeners() {
        window.addEventListener('resize', debounce(() => this.render(), 200));
    }

    clear() {
        this.svg.selectAll("*").remove();
    }

    parseData() {

    }

    render() {


    }
}

'use strict';

import * as d3 from 'd3';
import debounce from 'debounce';
import _ from 'lodash';

export const initHomeChart = () => {
    let chart = new HomeChart('reportChartSvg');
};

// pads the data with all the indexes for a year
const padYears = (data) => {
    _.forEach(data, (months, key) => {
        _.times(12, (index) => {
            let month = index + 1;

            if(_.isUndefined(months[month])) {
                months = {
                    ...months,
                    [month]: 0
                };
            }
        });

        data = {
            ...data,
            [key]: months
        };

    });

    return data;
};

class HomeChart {

    constructor(id) {
        this.svg = d3.select('#' + id);

        this.addListeners();

        this.request();
    }

    /**
     * Listen to resize events
     */
    addListeners() {
        window.addEventListener('resize', debounce(() => this.request(), 500));
    }

    /**
     * Request the data from the server then render it
     */
    request() {

        $.request('onGetChartReports', {
            data: {
                time: 'year'
            },
            success: (data) => {
                this.render(data);
            }
        });
    }

    render(data) {
        this.height = parseInt(this.svg.style('height'));
        this.width = parseInt(this.svg.style('width'));

        data = padYears(data);


    }

}
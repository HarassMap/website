'use strict';

import * as d3 from 'd3';
import debounce from 'debounce';
import _ from 'lodash';
import { BANNER_SWITCH, emitter } from '../utils/events';

export const initHomeChart = () => {
    let chart = new HomeChart('reportChartSvg');
};

// pads the data with all the indexes for a year
const padYears = (data) => {
    _.forEach(data, (months, key) => {
        _.times(12, (index) => {
            let month = index + 1;

            if (_.isUndefined(months[month])) {
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
        emitter.on(BANNER_SWITCH, () => {
            this.render();
        });
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
                this.data = data;
                this.render();
            }
        });
    }

    clear() {
        this.svg.selectAll("*").remove();
    }

    render() {
        this.clear();

        this.height = parseInt(this.svg.style('height'));
        this.width = parseInt(this.svg.style('width'));
        this.top = 100;
        this.bottom = this.height - 40.5;

        this.data = padYears(this.data);
        this.max = _.max(_.flatten(_.map(this.data, _.values)));

        this.x = d3.scaleTime().domain([new Date(new Date().setFullYear(new Date().getFullYear() - 1)), new Date()]).range([0, this.width]);
        this.y = d3.scaleLinear().domain([0, this.max]).range([this.bottom, this.top]);

        this.drawAxis();
        this.drawBaseLines();

    }

    drawAxis() {
        let xAxis = d3.axisBottom()
            .scale(this.x)
            .ticks(d3.timeMonth)
            .tickSize(10, 0)
            .tickFormat(d3.timeFormat("%b"));

        this.svg.append("g")
            .attr("class", "axis axis--x")
            .attr("transform", "translate(0," + this.bottom + ")")
            .call(xAxis)
            .call((g) => {
                g.select('.domain').remove();
            })
            .selectAll(".tick text")
            .style("text-anchor", "middle")
            .attr("x", 0)
            .attr("y", 15);
    }

    drawBaseLines() {
        this.baselineY = d3.line()
            .x((d) => d)
            .y((d) => this.bottom)
            .curve(d3.curveMonotoneX);

        this.baselineX = d3.line()
            .x((d) => 0)
            .y((d) => this.y(d))
            .curve(d3.curveMonotoneX);

        this.svg.append('path')
            .datum([0, this.width])
            .attr('class', 'base_line')
            .attr('fill', 'none')
            .attr('d', this.baselineY);

        this.svg.append('path')
            .datum(_.times(this.max + 1))
            .attr('class', 'base_line')
            .attr('fill', 'none')
            .attr('d', this.baselineX);
    }

}
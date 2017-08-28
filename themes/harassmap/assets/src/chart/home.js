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

        this.incidents = getIncidents(this.data);
        this.interventions = getInterventions(this.data);

        this.x = d3.scaleTime().domain([new Date(new Date().setFullYear(new Date().getFullYear() - 1)), new Date().setDate(1)]).range([0, this.width]);
        this.y = d3.scaleLinear().domain([0, this.max]).range([this.bottom, this.top]);

        this.drawAxis();
        this.drawBaseLines();
        this.drawGraph();

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
            .attr('class', 'base_line base_line--x')
            .attr('d', this.baselineY);

        this.svg.append('path')
            .datum(_.times(this.max + 1))
            .attr('class', 'base_line base_line--y')
            .attr('d', this.baselineX);
    }

    drawGraph() {
        this.area = d3.area()
            .x((data) => this.x(data.date))
            .y0(this.bottom)
            .y1((data) => this.y(data.count))
            .curve(d3.curveBasis);

        this.svg.append('g').append('path')
            .datum(this.interventions)
            .attr('class', 'line line--intervention')
            .attr('d', this.area);

        this.line = d3.line()
            .x((data) => this.x(data.date))
            .y((data) => this.y(data.count))
            .curve(d3.curveBasis);

        this.svg.append('path')
            .datum(this.incidents)
            .attr('class', 'line line--incident')
            .attr('d', this.line)
            .attr('fill', 'none');
    }
}

const getIncidents = (data) => {
    return getResults(data['incident']);
};

const getInterventions = (data) => {
    return getResults(data['intervention']);
};

const getResults = (incidents) => {
    let month = new Date().getMonth() + 1;
    let results = [];

    _.forEach(incidents, (count, index) => {
        index = parseInt(index);
        let date = new Date();

        if (index > month) {
            date = new Date(new Date().setFullYear(new Date().getFullYear() - 1));
        }

        date.setMonth(index - 2);

        results.push({count, date});
    });

    return _.orderBy(results, 'date');
};
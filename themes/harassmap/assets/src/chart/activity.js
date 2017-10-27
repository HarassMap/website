'use strict';

import * as d3 from 'd3';
import debounce from 'debounce';
import _ from 'lodash';

const PADDING_TOP = 10;
const PADDING_BOTTOM = 10;
const PADDING_LEFT = 0;
const PADDING_RIGHT = 0;
const TEXT_PADDING = 20;
const BAR_PADDING = 4;
const AXIS_PADDING = 5;

export class ActivityChart {

    constructor(id, data) {
        this.svg = d3.select('#' + id);
        this.ready = false;

        this.addListeners();

        this.parseData(data);
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

    parseData(data) {
        this.data = d3
            .nest()
            .key(({date}) => d3.timeDay.floor(date))
            .entries(this.padDates(data))
            .map(({key, values}) => ({
                date: new Date(key),
                value: d3.sum(values, (data) => data.count)
            }));

        this.render();
    }

    render() {
        this.clear();

        this.height = parseInt(this.svg.style('height'));
        this.width = parseInt(this.svg.style('width'));

        this.top = PADDING_TOP;
        this.bottom = this.height - PADDING_BOTTOM;
        this.left = PADDING_LEFT;
        this.right = this.width - PADDING_RIGHT;

        this.extent = d3.extent(this.data, ({date}) => date);

        this.max = d3.max(this.data, ({value}) => value);

        this.x = d3.scaleTime()
            .domain(this.extent)
            .range([this.left, this.right]);

        this.y = d3.scaleLinear()
            .domain([0, this.max])
            .range([this.bottom, this.top]);

        this.drawGraph();
        this.drawAxis();
    }

    drawGraph() {
        this.svg.selectAll(".bar")
            .data(this.data)
            .enter()
            .append("rect")
            .attr("class", "bar")
            .attr("x", ({date}) => this.x(date))
            .attr("y", ({value}) => this.y(value))
            .attr("rx", 10)
            .attr("ry", 10)
            .attr("width", ((this.width / this.data.length) - BAR_PADDING))
            .attr("height", ({value}) => (this.bottom - this.y(value) - AXIS_PADDING));
    }

    drawAxis() {
        this.xAxis = d3.axisBottom()
            .scale(this.x)
            .tickSizeOuter(0)
            .ticks(0);

        this.gX = this.svg.append("g")
            .attr("class", "axis axis--x")
            .attr("transform", "translate(0," + this.bottom + ")")
            .call(this.xAxis);

        this.gX.selectAll(".tick text")
            .style("text-anchor", "middle")
            .attr("x", 0)
            .attr("y", 15);
    }

    padDates(data) {
        data = _.map(data, ({count, day}) => ({
            date: new Date(day),
            count: count
        }));

        // get today's date
        let today = new Date();
        today.setDate(today.getDate() + 1);
        today.setHours(0, 0, 0, 0);

        let lastDate = new Date();
        lastDate.setDate(lastDate.getDate() - 29);
        lastDate.setHours(0, 0, 0, 0);

        do {
            let index = _.findIndex(data, {date: lastDate});

            if (index === -1) {
                data.push({
                    date: lastDate,
                    count: 0
                });
            }

            lastDate = new Date(lastDate.getFullYear(), lastDate.getMonth(), lastDate.getDate());
            lastDate.setDate(lastDate.getDate() + 1);
            lastDate.setHours(0, 0, 0, 0);
        } while (lastDate.getTime() <= today.getTime());

        return _.orderBy(data, 'date');

    }
}

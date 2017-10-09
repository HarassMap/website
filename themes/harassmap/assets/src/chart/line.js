'use strict';

import * as d3 from 'd3';
import debounce from 'debounce';
import _ from 'lodash';

const PADDING_TOP = 0;
const PADDING_BOTTOM = 10;
const PADDING_LEFT = 0;
const PADDING_RIGHT = 0;

export class LineChart {

    constructor(element, index) {
        this.svg = d3.select(element);
        this.ready = false;
        this.index = index;

        this.addListeners();

        this.parseData(JSON.parse(element.dataset.items));
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
        this.data = d3.nest().key(({date}) => d3.timeMonth.floor(date)).entries(this.padDates(data)).map(({key, values}) => {
            return {
                date: new Date(key),
                value: d3.sum(values, (data) => data.count)
            }
        });

        this.render();
    }

    render() {
        this.height = parseInt(this.svg.style('height'));
        this.width = parseInt(this.svg.style('width'));
        this.top = PADDING_TOP;
        this.bottom = this.height - PADDING_BOTTOM;
        this.left = PADDING_LEFT;
        this.right = this.width - PADDING_RIGHT;

        let extent = d3.extent(this.data, ({date}) => date);

        this.x = d3.scaleTime()
            .domain(extent)
            .range([this.left, this.right]);

        let max = d3.max(this.data, (data) => data.value);

        this.y = d3.scaleLinear()
            .range([this.bottom, this.top])
            .domain([0, (max + Math.ceil(max / 10))]);

        this.drawClip();
        this.drawGraph();
        this.drawAxis();
    }

    drawClip() {
        this.clip = this.svg.append("svg:clipPath")
            .attr("id", "lineclip" + this.index)
            .append("svg:rect")
            .attr("x", this.left)
            .attr("y", this.top)
            .attr("width", this.right - this.left)
            .attr("height", this.bottom - this.top);

        this.chartBody = this.svg.append("g")
            .attr("clip-path", "url(#lineclip" + this.index + ")");
    }

    drawGraph() {
        this.line = d3.line()
            .x(({date}) => this.x(date))
            .y(({value}) => this.y(value))
            .curve(d3.curveBasis);

        this.lineG = this.chartBody.append('path')
            .datum(this.data)
            .attr('class', 'line')
            .attr('stroke', '#000')
            .attr('fill', 'none')
            .attr('d', this.line);
    }

    drawAxis() {
        this.xAxis = d3.axisBottom()
            .scale(this.x)
            .ticks(3);

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
        data = _.orderBy(_.map(data, ({count, year, month}) => ({
            date: new Date(year, month),
            count: count
        })), 'date');

        // get 2 years ago
        let lastDate = new Date();
        lastDate.setFullYear(lastDate.getFullYear() - 2);
        lastDate.setDate(1);
        lastDate.setHours(0, 0, 0, 0);

        const addMonths = (date) => {
            // get yesterday
            let lastMonth = new Date(date.getTime());
            lastMonth.setMonth(lastMonth.getMonth() - 1);

            if (lastDate.getTime() !== date.getTime() && lastDate.getTime() !== lastMonth.getTime()) {
                let monthAfter = new Date(lastDate.getTime());
                monthAfter.setMonth(monthAfter.getMonth() + 1);
                data = [
                    ...data,
                    {count: 0, date: monthAfter}
                ];

                lastDate = monthAfter;

                addMonths(date);
            } else {
                lastDate = date;
            }
        };

        _.forEach(data, ({date}) => {
            addMonths(date);
        });


        return _.orderBy(data, 'date');

    }
}

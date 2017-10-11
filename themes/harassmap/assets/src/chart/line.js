'use strict';

import * as d3 from 'd3';
import debounce from 'debounce';
import Handlebars from "handlebars";
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
        this.clear();

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
        this.drawLabels();
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
            .curve(d3.curveCardinal);

        this.lineG = this.chartBody.append('path')
            .datum(this.data)
            .attr('class', 'line')
            .attr('fill', 'none')
            .attr('d', this.line);
    }

    drawAxis() {
        this.xAxis = d3.axisBottom()
            .scale(this.x)
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

    drawLabels() {
        let source = $("#line-chart-popover").html();
        let template = Handlebars.compile(source);

        this.focus = this.svg
            .append("g")
            .attr("class", "focus line-popover")
            .style("display", "none");

        this.focus
            .append("circle")
            .attr("r", 4.5);

        this.tether = this.svg
            .append("g")
            .attr("class", "tether");

        this.mouseRect = this.svg
            .append("rect")
            .attr("class", "overlay")
            .attr("fill", "none")
            .attr("pointer-events", "all")
            .attr("width", this.width)
            .attr("height", this.height)
            .on("mouseover", () => this.focus.style("display", null))
            .on("mouseout", () => mouseout())
            .on("mousemove", () => mousemove());

        let oldData = {};
        let $focus = $(this.tether.node());
        $focus.popover({
            placement: 'top',
            trigger: 'manual'
        });

        const mouseout = () => {
            this.focus.style("display", "none");
            $focus.popover('hide');
            oldData = {};
        };
        const mousemove = () => {
            const bisectDate = d3.bisector(({date}) => date).left;
            let x0 = this.x.invert(d3.mouse(this.mouseRect.node())[0]),
                i = bisectDate(this.data, x0, 1),
                d0 = this.data[i - 1],
                d1 = this.data[i],
                d = x0 - d0.date > d1.date - x0 ? d1 : d0;

            this.focus
                .attr("transform", "translate(" + this.x(d.date) + "," + this.y(d.value) + ")");

            this.tether
                .attr("transform", "translate(" + this.x(d.date) + ",0)")
                .attr('data-content', () => template({
                    total: d.value,
                    single: d.value === 1,
                    plural: d.value < 11
                }));

            if (oldData !== d) {
                oldData = d;
                $focus.popover('show');
            }

        };
    }

    padDates(data) {
        data = _.orderBy(_.map(data, ({count, year, month}) => ({
            date: new Date(year, month),
            count: count
        })), 'date');

        let nextMonth = new Date();
        nextMonth.setMonth(nextMonth.getMonth() + 1);
        nextMonth.setDate(1);
        nextMonth.setHours(0, 0, 0, 0);

        data.push({
            date: nextMonth,
            count: 0
        });

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

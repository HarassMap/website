'use strict';

import * as d3 from 'd3';
import debounce from 'debounce';
import Handlebars from "handlebars";
import _ from 'lodash';
import { BANNER_SWITCH, emitter } from '../utils/events';

// zoom levels for the different markers
const ZOOM_YEAR = 1;
const ZOOM_MONTH = 2;
const ZOOM_WEEK = 40;

const INITIAL_YEAR = 2009;

const PADDING_TOP = 90;
const PADDING_BOTTOM = 40;
const PADDING_LEFT = 30;
const PADDING_RIGHT = 10;

export class HomeChart {

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
        emitter.on(BANNER_SWITCH, () => {
            this.render();
        });

        const $filters = $('.report-chart-html .filter');

        $filters.on('click', (event) => {
            event.preventDefault();
            let now = new Date();
            let yearStart = new Date();
            let yearEnd = new Date();

            const $target = $(event.target);

            $filters.removeClass('active');
            $target.addClass('active');

            if($target.hasClass('filter-year')) {

                yearStart.setFullYear(INITIAL_YEAR);
                yearStart.setMonth(0);
                yearStart.setDate(1);

                yearEnd.setMonth(now.getMonth() + 1);
                yearEnd.setDate(0);

            } else if ($target.hasClass('filter-month')) {

                yearStart.setFullYear(now.getFullYear() - 1);
                yearStart.setDate(1);

                yearEnd.setMonth(now.getMonth() + 1);
                yearEnd.setDate(0);

            } else if ($target.hasClass('filter-week')) {

                yearStart.setFullYear(now.getFullYear());
                yearStart.setMonth(now.getMonth() - 1);
                yearStart.setDate(1);

                yearEnd.setMonth(now.getMonth() + 1);
                yearEnd.setDate(0);

            }

            yearStart.setHours(0, 0, 0, 0);
            yearEnd.setHours(0, 0, 0, 0);

            this.svg.call(
                this.zoom.transform,
                d3.zoomIdentity
                    .scale(this.width / (this.x(yearEnd) - this.x(yearStart)))
                    .translate(-this.x(yearStart), 0)
            );
        });
    }

    clear() {
        this.svg.selectAll("*").remove();
    }

    parseData(data) {
        this.data = data;

        this.extent = d3.extent(_.map(_.concat(_.keys(this.data['incident']), _.keys(this.data['intervention']))), (date) => new Date(date));
        this.incidents = this.getIncidents();
        this.interventions = this.getInterventions();

        this.yearly = {
            incidents: d3.nest().key((data) => d3.timeYear.floor(data.date)).entries(this.incidents).map(this.meanValues),
            interventions: d3.nest().key((data) => d3.timeYear.floor(data.date)).entries(this.interventions).map(this.meanValues)
        };

        this.monthly = {
            incidents: d3.nest().key((data) => d3.timeMonth.floor(data.date)).entries(this.incidents).map(this.meanValues),
            interventions: d3.nest().key((data) => d3.timeMonth.floor(data.date)).entries(this.interventions).map(this.meanValues)
        };

        this.weekly = {
            incidents: d3.nest().key((data) => d3.timeWeek.floor(data.date)).entries(this.incidents).map(this.meanValues),
            interventions: d3.nest().key((data) => d3.timeWeek.floor(data.date)).entries(this.interventions).map(this.meanValues)
        };

        this.ready = true;
        this.render();
        this.animate();
    }

    meanValues({key, values}) {
        return {
            date: new Date(key),
            value: d3.sum(values, (data) => data.count)
        }
    }

    render() {
        // if the data isn't ready then just stop here
        if (!this.ready) {
            return;
        }

        // clear the canvas
        this.clear();

        this.currentZoom = ZOOM_YEAR;
        this.height = parseInt(this.svg.style('height'));
        this.width = parseInt(this.svg.style('width'));
        this.top = PADDING_TOP;
        this.bottom = this.height - PADDING_BOTTOM;
        this.left = PADDING_LEFT;
        this.right = this.width - PADDING_RIGHT;

        this.x = d3.scaleTime()
            .domain(this.extent)
            .range([this.left, this.right]);

        this.y = d3.scaleLinear()
            .domain([0, 0])
            .range([this.bottom, this.top]);

        this.drawClip();

        this.drawGraph();
        this.drawAxis();
        this.addBehaviours();

        this.markers = this.chartBody.append('g');

        this.redraw();
    }

    redraw() {
        let {incidents, interventions} = this.getData();

        let max = d3.max(_.concat(incidents, interventions), (data) => data.value);

        this.y.domain([0, (max + Math.ceil(max / 10))]);

        this.draw(incidents, interventions);
    }

    drawClip() {
        this.clip = this.svg.append("svg:clipPath")
            .attr("id", "clip")
            .append("svg:rect")
            .attr("x", this.left)
            .attr("y", this.top)
            .attr("width", this.right - this.left)
            .attr("height", this.bottom - this.top);

        this.chartBody = this.svg.append("g")
            .attr("clip-path", "url(#clip)");
    }

    drawAxis() {
        this.xAxis = d3.axisBottom()
            .scale(this.x);

        if (this.width < 500) {
            this.xAxis.ticks(3);
        } else {
            this.xAxis.ticks(12);
        }

        this.yAxis = d3.axisLeft()
            .ticks(5)
            .scale(this.y);

        this.gX = this.svg.append("g")
            .attr("class", "axis axis--x")
            .attr("transform", "translate(0," + this.bottom + ")")
            .call(this.xAxis);

        this.gX.selectAll(".tick text")
            .style("text-anchor", "middle")
            .attr("x", 0)
            .attr("y", 15);

        this.gY = this.svg.append("g")
            .attr("class", "axis axis--y")
            .attr("transform", "translate(" + this.left + ",0)")
            .call(this.yAxis);
    }

    getData() {
        let data = this.yearly;

        switch (this.currentZoom) {
            case ZOOM_MONTH:
                data = this.monthly;
                break;
            case ZOOM_WEEK:
                data = this.weekly;
                break;
        }

        return data;
    }

    drawGraph() {

        this.line = d3.line()
            .x((data) => this.x(data.date))
            .y((data) => this.y(data.value))
            .curve(d3.curveCardinal);

        this.area = d3.area()
            .x((data) => this.x(data.date))
            .y0(this.bottom)
            .y1((data) => this.y(data.value))
            .curve(d3.curveCardinal);

        this.areaG = this.chartBody
            .append('path')
            .attr('class', 'line line--intervention');

        this.lineG = this.chartBody.append('path')
            .attr('class', 'line line--incident')
            .attr('fill', 'none');
    }

    draw(incidents, interventions) {

        // axis
        this.xAxis.scale(this.x);
        this.gX.call(this.xAxis);
        this.yAxis.scale(this.y);
        this.gY.call(this.yAxis);

        // draw
        this.lineG.datum(incidents).attr('d', this.line);
        this.areaG.datum(interventions).attr('d', this.area);

        this.drawMarkers(incidents, interventions);
    }

    drawMarkers(incidents, interventions) {
        let source = $("#chart-popover").html();
        let template = Handlebars.compile(source);

        let dotsIncidents = this.markers.selectAll(".circle--incident").data(incidents);
        dotsIncidents.enter().append("circle")
            .attr("r", 3.5)
            .attr('class', 'circle circle--incident')
            .attr('data-toggle', 'popover')
            .attr('data-placement', 'top')
            .attr('data-trigger', 'hover')
            .merge(dotsIncidents)
            .attr('data-content', ({value}) => template({
                total: value,
                incident: true,
                single: value === 1,
                plural: value < 11
            }))
            .attr("cx", ({date}) => this.x(date))
            .attr("cy", ({value}) => this.y(value));
        dotsIncidents.exit().remove();

        let dotsInterventions = this.markers.selectAll(".circle--intervention").data(interventions);
        dotsInterventions.enter().append("circle")
            .attr("r", 3.5)
            .attr('class', 'circle circle--intervention')
            .attr('data-toggle', 'popover')
            .attr('data-placement', 'top')
            .attr('data-trigger', 'hover')
            .merge(dotsInterventions)
            .attr('data-content', ({value}) => template({
                total: value,
                incident: false,
                single: value === 1,
                plural: value < 11
            }))
            .attr("cx", ({date}) => this.x(date))
            .attr("cy", ({value}) => this.y(value));
        dotsInterventions.exit().remove();

        $('[data-toggle="popover"]').popover();
    }

    addBehaviours() {
        const zoomed = () => {
            let xTransform = d3.event.transform.rescaleX(this.x);

            // redraw if we need to
            if (this.shouldRedraw()) {
                this.redraw();
            }

            this.gX.call(this.xAxis.scale(d3.event.transform.rescaleX(this.x)));

            this.areaG.attr("d", this.area.x((data) => xTransform(data.date)));
            this.lineG.attr("d", this.line.x((data) => xTransform(data.date)));

            // move the markers
            this.markers.selectAll(".circle--incident").attr("cx", (data) => xTransform(data.date));
            this.markers.selectAll(".circle--intervention").attr("cx", (data) => xTransform(data.date));
        };

        this.zoom = d3.zoom()
            .scaleExtent([1, 250])
            .translateExtent([
                [0, 0],
                [this.width, this.height]
            ])
            .on("zoom", zoomed);

        this.svg.call(this.zoom)
            .on("wheel.zoom", null)
            .on("dblclick.zoom", null)
            .on("touchmove.zoom", null);
    }

    animate() {
        let now = new Date();

        let yearStart = new Date(INITIAL_YEAR, 0, 1);
        yearStart.setHours(0, 0, 0, 0);

        let yearEnd = new Date(now.getFullYear(), now.getMonth() + 1, 0);
        yearEnd.setHours(0, 0, 0, 0);

        this.svg.call(
            this.zoom.transform,
            d3.zoomIdentity
                .scale(this.width / (this.x(yearEnd) - this.x(yearStart)))
                .translate(-this.x(yearStart), 0)
        );
    }

    getZoom() {
        return d3.zoomTransform(this.svg.node()).k;
    }

    shouldRedraw() {
        let zoom = this.getZoom();

        if ((zoom > ZOOM_YEAR && zoom < ZOOM_MONTH) && this.currentZoom !== ZOOM_YEAR) {
            this.currentZoom = ZOOM_YEAR;
            return true;
        } else if ((zoom > ZOOM_MONTH && zoom < ZOOM_WEEK) && this.currentZoom !== ZOOM_MONTH) {
            this.currentZoom = ZOOM_MONTH;
            return true;
        } else if ((zoom > ZOOM_WEEK) && this.currentZoom !== ZOOM_WEEK) {
            this.currentZoom = ZOOM_WEEK;
            return true;
        }

        return false;
    }

    getIncidents() {
        return this.getResults(this.data['incident']);
    };

    getInterventions() {
        return this.getResults(this.data['intervention']);
    };

    getResults(data) {
        let results = [];

        _.forEach(data, (count, date) => {

            date = new Date(date);
            date.setHours(0, 0, 0, 0);

            let index = _.findIndex(results, {'date': date});

            if (index !== -1) {
                results[index].count += count;
            } else {
                results.push({
                    'date': date,
                    'count': count
                });
            }

        });

        return this.padDates(results);
    }

    padDates(data) {
        data = _.orderBy(data, 'date');
        let first = _.first(data);

        // add a super old date
        let lastDate = new Date(first.date.getTime());
        lastDate.setFullYear(lastDate.getFullYear() - 10);
        data = [{count: 0, date: lastDate}, ...data];

        // add the day before the first item
        lastDate = new Date(first.date.getTime());
        lastDate.setDate(lastDate.getDate() - 1);
        data = [{count: 0, date: lastDate}, ...data];

        const addDays = (date) => {
            // get yesterday
            let yesterday = new Date(date.getTime());
            yesterday.setDate(yesterday.getDate() - 1);

            if (lastDate.getTime() !== date.getTime() && lastDate.getTime() !== yesterday.getTime()) {
                let dayAfter = new Date(lastDate.getTime());
                dayAfter.setDate(dayAfter.getDate() + 1);
                data = [
                    ...data,
                    {count: 0, date: dayAfter}
                ];

                lastDate = dayAfter;

                addDays(date);
            } else {
                lastDate = date;
            }
        };

        _.forEach(data, ({date}, index) => {
            if (index > 1) {
                addDays(date);
            }
        });

        return _.orderBy(data, 'date');
    }
}

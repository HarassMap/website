'use strict';

import * as d3 from 'd3';
import debounce from 'debounce';
import Handlebars from "handlebars";
import _ from 'lodash';
import { BANNER_SWITCH, emitter } from '../utils/events';

export const initHomeChart = () => {
    let chart = new HomeChart('reportChartSvg');
};

// zoom levels for the different markers
const ZOOM_YEAR = 1;
const ZOOM_MONTH = 2;
const ZOOM_WEEK = 23;

const INITIAL_YEAR = 2009;

const PADDING_TOP = 90;
const PADDING_BOTTOM = 40;
const PADDING_LEFT = 30;
const PADDING_RIGHT = 10;

const cache = {
    incidents: {},
    interventions: {}
};

class HomeChart {

    constructor(id) {
        this.svg = d3.select('#' + id);
        this.html = $('.report-chart-html');
        this.ready = false;

        this.addListeners();

        this.request();
    }

    /**
     * Listen to resize events
     */
    addListeners() {
        window.addEventListener('resize', debounce(() => this.render(), 200));
        emitter.on(BANNER_SWITCH, () => {
            this.render();
            this.animate();
        });
    }

    /**
     * Request the data from the server then render it
     */
    request() {
        $.request('onGetChartReports', {
            success: (data) => {
                this.parseData(data);
            }
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
        this.ready = true;
        this.render();
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
        this.redraw();
        this.addBehaviours();
    }

    drawClip() {
        this.clip = this.svg.append("svg:clipPath")
            .attr("id", "clip")
            .append("svg:rect")
            .attr("x", this.left)
            .attr("y", this.top)
            .attr("width", this.right - this.left)
            .attr("height", this.bottom - this.top);
    }

    drawAxis() {

        if (this.gX) this.gX.remove();
        if (this.gY) this.gY.remove();

        this.xAxis = d3.axisBottom()
            .scale(this.x)
            .ticks(12);

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

    redraw() {
        let incidentData = this.getIncidentData();
        let interventionData = this.getInterventionData();

        this.normalIncidents = incidentData.results;
        this.normalInterventions = interventionData.results;

        let max = Math.max(incidentData.max, interventionData.max);

        this.y.domain([0, (max + Math.ceil(max / 50))]);

        this.drawGraph();
        this.drawMarkers();
        this.drawAxis();
    }

    drawGraph() {
        this.chartBody = this.svg.append("g")
            .attr("clip-path", "url(#clip)");

        // remove old lines
        if (this.lineG) this.lineG.remove();
        if (this.areaG) this.areaG.remove();

        this.line = d3.line()
            .x((data) => this.x(data.date))
            .y((data) => this.y(data.count))
            .curve(d3.curveMonotoneX);

        this.area = d3.area()
            .x((data) => this.x(data.date))
            .y0(this.bottom)
            .y1((data) => this.y(data.count))
            .curve(d3.curveMonotoneX);

        this.areaG = this.chartBody
            .append('path')
            .datum(this.normalInterventions)
            .attr('class', 'line line--intervention')
            .attr('d', this.area);

        this.lineG = this.chartBody.append('path')
            .datum(this.normalIncidents)
            .attr('class', 'line line--incident')
            .attr('d', this.line)
            .attr('fill', 'none');
    }

    drawMarkers() {
        let source = $("#chart-popover").html();
        let template = Handlebars.compile(source);

        if (this.dotsIncidents) this.dotsIncidents.remove();
        if (this.dotsInterventions) this.dotsInterventions.remove();

        this.dotsIncidents = this.chartBody.append('g')
            .selectAll("dot")
            .data(this.normalIncidents)
            .enter().append("circle")
            .attr("r", 3.5)
            .attr('class', 'circle circle--incident')
            .attr('data-toggle', 'popover')
            .attr('data-content', data => template({total: data.count, incident: true, plural: data.count !== 1}))
            .attr('data-placement', 'top')
            .attr('data-trigger', 'hover')
            .attr("cx", data => this.x(data.date))
            .attr("cy", data => this.y(data.count));

        this.dotsInterventions = this.chartBody.append('g')
            .selectAll("dot")
            .data(this.normalInterventions)
            .enter().append("circle")
            .attr("r", 3.5)
            .attr('class', 'circle circle--intervention')
            .attr('data-toggle', 'popover')
            .attr('data-content', data => template({total: data.count, incident: false, plural: data.count !== 1}))
            .attr('data-placement', 'top')
            .attr('data-trigger', 'hover')
            .attr("cx", data => this.x(data.date))
            .attr("cy", data => this.y(data.count));

        $('[data-toggle="popover"]').popover();
    }

    addBehaviours() {
        const zoomed = () => {
            let xTransform = d3.event.transform.rescaleX(this.x);

            // redraw if we need to
            if (this.shouldRedraw()) {
                console.debug('redraw');
                this.redraw();
            }

            this.gX.call(this.xAxis.scale(d3.event.transform.rescaleX(this.x)));

            this.areaG.attr("d", this.area.x((data) => xTransform(data.date)));
            this.lineG.attr("d", this.line.x((data) => xTransform(data.date)));

            this.dotsIncidents.attr("cx", (data) => xTransform(data.date));
            this.dotsInterventions.attr("cx", (data) => xTransform(data.date));
        };

        this.zoom = d3.zoom()
            .scaleExtent([1, 250])
            .translateExtent([[0, 0], [this.width, this.height]])
            .on("zoom", zoomed);

        this.svg.call(this.zoom);
    }

    animate() {
        let now = new Date();

        let yearStart = new Date(INITIAL_YEAR, 0, 1);
        yearStart.setHours(0, 0, 0, 0);

        let yearEnd = new Date(now.getFullYear(), now.getMonth() + 1, 0);
        yearEnd.setHours(0, 0, 0, 0);

        this.svg
            .call(
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

    getIncidentData() {
        let data = [];

        if (cache.incidents[this.currentZoom]) {
            console.debug('getting from cache');
            data = cache.incidents[this.currentZoom];
        } else {
            data = cache.incidents[this.currentZoom] = this.getDataPoints(this.incidents);
        }

        return data;
    }

    getInterventionData() {
        let data = [];

        if (cache.interventions[this.currentZoom]) {
            data = cache.interventions[this.currentZoom];
        } else {
            data = cache.interventions[this.currentZoom] = this.getDataPoints(this.interventions);
        }

        return data;
    }

    getDataPoints(data) {
        let results = [];
        let max = 0;

        _.forEach(data, (item) => {
            let total = 0;
            let date = this.parseDate(item.date);
            let index = _.findIndex(results, {date});

            if (index === -1) {
                total = item.count;

                results.push({
                    count: item.count,
                    date: date
                });
            } else {
                total = results[index].count += item.count;
            }

            if (total > max) {
                max = total;
            }
        });

        return {
            results,
            max
        };
    }

    parseDate(date) {
        let newDate = new Date(date.getTime());
        newDate.setHours(0, 0, 0, 0);

        if (this.currentZoom === ZOOM_YEAR) {
            newDate.setFullYear(newDate.getFullYear());
            newDate.setMonth(0);
            newDate.setDate(1);
        } else if (this.currentZoom === ZOOM_MONTH) {
            newDate.setDate(1);
        } else if (this.currentZoom === ZOOM_WEEK) {
            let day = date.getDay();
            newDate.setDate(date.getDate() - day + (day === 0 ? -6 : 1));
        }

        return newDate;
    }
}
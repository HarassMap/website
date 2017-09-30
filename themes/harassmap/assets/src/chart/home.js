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
const ZOOM_DAY = 100;

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
            // this.animate();
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

        this.max = 0;

        this.extent = d3.extent(_.map(_.concat(_.keys(this.data['incident']), _.keys(this.data['intervention']))), (date) => new Date(date));
        this.incidents = this.getIncidents();
        this.interventions = this.getInterventions();

        console.debug(this.incidents);

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

        this.height = parseInt(this.svg.style('height'));
        this.width = parseInt(this.svg.style('width'));
        this.top = 110;
        this.bottom = this.height - 40.5;
        this.left = 30;
        this.right = this.width - 10;

        this.x = d3.scaleTime()
            .domain(this.extent)
            .range([this.left, this.right]);

        this.y = d3.scaleLinear()
            .domain([0, this.max + 1])
            .range([this.bottom, this.top]);

        this.drawClip();
        this.drawGraph();
        this.drawAxis();
        this.drawMarkers();
        this.addBehaviours();
    }

    redraw() {

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

    drawGraph() {
        this.chartBody = this.svg.append("g")
            .attr("clip-path", "url(#clip)");

        this.area = d3.area()
            .x((data) => this.x(data.date))
            .y0(this.bottom)
            .y1((data) => this.y(data.count))
            .curve(d3.curveMonotoneX);

        this.areaG = this.chartBody
            .append('path')
            .datum(this.interventions)
            .attr('class', 'line line--intervention')
            .attr('d', this.area);

        this.line = d3.line()
            .x((data) => this.x(data.date))
            .y((data) => this.y(data.count))
            .curve(d3.curveMonotoneX);

        this.lineG = this.chartBody.append('path')
            .datum(this.incidents)
            .attr('class', 'line line--incident')
            .attr('d', this.line)
            .attr('fill', 'none');
    }

    drawMarkers() {
        let source = $("#chart-popover").html();
        let template = Handlebars.compile(source);

        if (this.dotsIncidents) {
            this.dotsIncidents.remove();
        }
        if (this.dotsInterventions) {
            this.dotsInterventions.remove();
        }

        this.dotsIncidents = this.chartBody.append('g')
            .selectAll("dot")
            .data(this.incidents)
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
            .data(this.interventions)
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

            this.redraw();

            this.areaG.attr("d", this.area.x((data) => xTransform(data.date)));
            this.lineG.attr("d", this.line.x((data) => xTransform(data.date)));
            this.dotsIncidents.attr("cx", (data) => xTransform(data.date));
            this.dotsInterventions.attr("cx", (data) => xTransform(data.date));

            this.gX.call(this.xAxis.scale(d3.event.transform.rescaleX(this.x)));
        };

        this.zoom = d3.zoom()
            .scaleExtent([1, 1000])
            .translateExtent([[0, 0], [this.width, this.height]])
            .on("zoom", zoomed);

        this.svg.call(this.zoom);
    }

    animate() {
        let now = new Date();

        let yearStart = new Date(now.getFullYear() - 1, now.getMonth() + 1, 1);
        yearStart.setHours(0, 0, 0, 0);

        let yearEnd = new Date(now.getFullYear(), now.getMonth() + 1, 1);
        yearEnd.setHours(0, 0, 0, 0);

        this.svg
            .call(this.zoom.transform, d3.zoomIdentity.scale(this.width / (this.x(yearEnd) - this.x(yearStart)))
                .translate(-this.x(yearStart), 0));
    }

    getZoom() {
        return d3.zoomTransform(this.svg.node()).k;
    }

    getIncidents() {
        return this.getResults(this.data['incident']);
    };

    getInterventions() {
        return this.getResults(this.data['intervention']);
    };

    getResults(data) {
        let results = [];
        // new Date(first.date.getFullYear(), first.date.getMonth(), first.date.getDate() - 1)

        _.forEach(data, (count, date) => {
            let total = 0;

            date = parseDate(date);

            let index = _.findIndex(results, {'date': date});

            if (index !== -1) {
                total = results[index].count += count;
            } else {
                total = count;

                results.push({
                    'date': date,
                    'count': count
                });
            }

            if (total > this.max) {
                this.max = total;
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

const parseDate = (dateString) => {
    let date = new Date(dateString);

    date.setHours(0, 0, 0, 0);

    return date;
};
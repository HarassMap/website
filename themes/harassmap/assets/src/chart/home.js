'use strict';

import * as d3 from 'd3';
import debounce from 'debounce';
import Handlebars from "handlebars";
import _ from 'lodash';
import { BANNER_SWITCH, emitter } from '../utils/events';

export const initHomeChart = () => {
    let chart = new HomeChart('reportChartSvg');
};

const YEAR = 'year';
const MONTH = 'month';
const DAY = 'day';

class HomeChart {

    constructor(id) {
        this.svg = d3.select('#' + id);
        this.html = $('.report-chart-html');
        this.mode = YEAR;

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
        });
    }

    /**
     * Request the data from the server then render it
     */
    request() {
        $.request('onGetChartReports', {
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
        this.top = 110;
        this.bottom = this.height - 40.5;
        this.left = 10;
        this.right = this.width - 10;

        this.max = 0;

        this.incidents = getIncidents(this.data);
        this.interventions = getInterventions(this.data);

        let now = new Date();

        let start = _.first(this.incidents);

        let yearEnd = new Date(now.getFullYear(), now.getMonth(), 1);
        yearEnd.setHours(0, 0, 0, 0);

        this.x = d3.scaleTime()
            .domain([start.date, yearEnd])
            .range([this.left, this.right]);
        this.y = d3.scaleLinear()
            .domain([0, this.max])
            .range([this.bottom, this.top]);

        this.drawAxis();
        this.drawBaseLines();
        this.drawGraph();
        this.addBehaviours();
    }

    drawAxis() {
        this.xAxis = d3.axisBottom()
            .scale(this.x)
            .ticks(d3.timeMonth)
            .tickSize(10, 0)
            .tickFormat(data => _.toUpper(d3.timeFormat("%b")(data)));

        this.gX = this.svg.append("g")
            .attr("class", "axis axis--x")
            .attr("transform", "translate(0," + this.bottom + ")")
            .call(this.xAxis)
            .selectAll(".tick text")
            .style("text-anchor", "middle")
            .attr("x", 0)
            .attr("y", 15);
    }

    addBehaviours() {
        const zoomed = () => {
            this.areaG.attr("transform", d3.event.transform);
            this.lineG.attr("transform", d3.event.transform);
            this.gX.attr("transform", d3.event.transform);
            this.dotsIncidents.attr("transform", d3.event.transform);
            this.dotsInterventions.attr("transform", d3.event.transform);

            this.gX.call(this.xAxis.scale(d3.event.transform.rescaleX(this.x)));
        };

        let zoom = d3.zoom()
            .scaleExtent([1, 1])
            .translateExtent([[-100, 0], [this.width , this.height]])
            .on("zoom", zoomed);

        this.svg.call(zoom);
    }

    drawBaseLines() {
        this.baselineX = d3.line()
            .x((d) => d)
            .y((d) => this.bottom)
            .curve(d3.curveLinear);

        this.baselineY = d3.line()
            .x((d) => this.left + 0.5)
            .y((d) => this.y(d))
            .curve(d3.curveLinear);

        this.svg.append('path')
            .datum([this.left, this.right])
            .attr('class', 'base_line base_line--x')
            .attr('d', this.baselineX);

        this.svg.append('path')
            .datum(_.times(this.max + 1))
            .attr('class', 'base_line base_line--y')
            .attr('d', this.baselineY);
    }

    drawGraph() {
        this.area = d3.area()
            .x((data) => this.x(data.date))
            .y0(this.bottom)
            .y1((data) => this.y(data.count))
            .curve(d3.curveMonotoneX);

        this.areaG = this.svg.append('g').append('path')
            .datum(this.interventions)
            .attr('class', 'line line--intervention')
            .attr('d', this.area);

        this.line = d3.line()
            .x((data) => this.x(data.date))
            .y((data) => this.y(data.count))
            .curve(d3.curveMonotoneX);

        this.lineG = this.svg.append('path')
            .datum(this.incidents)
            .attr('class', 'line line--incident')
            .attr('d', this.line)
            .attr('fill', 'none');

        let source = $("#chart-popover").html();
        let template = Handlebars.compile(source);

        this.dotsIncidents = this.svg.append('g');
        this.dotsIncidents.selectAll("dot")
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

        this.dotsInterventions = this.svg.append('g');
        this.dotsInterventions.selectAll("dot")
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
}

const getIncidents = (data) => {
    return getResults(data['incident']);
};

const getInterventions = (data) => {
    return getResults(data['intervention']);
};

const getResults = (data) => {
    let results = [];

    _.forEach(data, (count, date) => {
        let total = 0;

        date = parseDate(date);

        let index = _.findIndex(results, {'date': date})

        if (index !== -1) {
            total = results[index].count += count;
        } else {
            results.push({
                'date': date,
                'count': count
            });
        }


    });

    return _.orderBy(results, 'date');
};

const parseDate = (dateString) => {
    let date = new Date(dateString);

    date.setDate(1);
    date.setHours(0, 0, 0, 0);

    return date;
};
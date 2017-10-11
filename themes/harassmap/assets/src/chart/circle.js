'use strict';

import * as d3 from 'd3';
import debounce from 'debounce';
import Handlebars from "handlebars";
import _ from 'lodash';

export class CircleChart {

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
        this.data = data;

        this.max = d3.max(this.data, ({count}) => count);
        this.ready = true;
        this.render();
    }

    render() {
        let source = $("#circle-chart-popover").html();
        let template = Handlebars.compile(source);

        this.clear();

        this.width = parseInt(this.svg.style('width'));
        this.height = parseInt(this.svg.style('height'));

        this.color = d3.scaleLinear()
            .domain([0, this.max])
            .interpolate(d3.interpolateHcl)
            .range([d3.rgb("#FFB6E3"), d3.rgb("#BA1D4A")]);

        this.pack = d3.pack()
            .size([this.width, this.height])
            .padding(10);

        this.hier = d3.hierarchy({children: this.data})
            .sum((data) => data.count);

        this.nodes = this.pack(this.hier);

        this.bubbles = this.svg.append("g")
            .attr("class", "bubbles");

        console.debug(this.nodes);

        this.bubbles.selectAll(".bubble")
            .data(this.nodes.children)
            .enter()
            .append("circle")
            .attr("class", "bubble")
            .attr("r", (data) => _.isNaN(data.r) ? 0 : data.r)
            .attr("cx", (data) => data.x)
            .attr("cy", (data) => data.y)
            .style("fill", (data) => this.color(data.value))
            .attr('data-toggle', 'popover')
            .attr('data-placement', 'top')
            .attr('data-trigger', 'hover')
            .attr('data-content', ({data}) => template({
                title: data.title,
                total: data.count,
                single: data.count === 1,
                plural: data.count < 11
            }));

        const {x, y, width, height} = this.bubbles.node().getBBox();
        this.svg.attr("viewBox", `${x} ${y} ${width} ${height}`);

        $('[data-toggle="popover"]').popover();

    }
}

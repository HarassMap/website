'use strict';

import { CircleChart } from './circle';
import { HomeChart } from './home';
import { LineChart } from './line';

export const initHomeChart = (data) => {
    new HomeChart('reportChartSvg', data);
};

export const initCircleChart = (data) => {
    new CircleChart('commonReportsCircleSvg', data);
};

export const initLineChart = () => {
    $('.report-common-line-svg').each(function() {
        new LineChart(this);
    });
};
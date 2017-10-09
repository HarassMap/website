'use strict';

import { CircleChart } from './circle';
import { HomeChart } from './home';

export const initHomeChart = (data) => {
    new HomeChart('reportChartSvg', data);
};

export const initCircleChart = (data) => {
    new CircleChart('commonReportsCircleSvg', data);
};
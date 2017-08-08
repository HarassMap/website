'use strict';

import ee from 'event-emitter';

const eventEmitter = function () {};
ee(eventEmitter.prototype);

export const emitter = new eventEmitter();

export const REFRESH_MAP = 'REFRESH_MAP';
export const FILTER_MAP = 'FILTER_MAP';
export const CENTER_MAP = 'CENTER_MAP';
export const MOVE_MARKER = 'MOVE_MARKER';
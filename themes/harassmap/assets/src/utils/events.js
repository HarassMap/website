'use strict';

import ee from 'event-emitter';

const eventEmitter = function () {};
ee(eventEmitter.prototype);

export const emitter = new eventEmitter();

export const REFRESH_MAP = 'REFRESH_MAP';
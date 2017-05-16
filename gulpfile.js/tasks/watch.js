'use strict';

var config = require('../config');

var gulp = require('gulp');
var path = require('path');

module.exports = function() {
    gulp.watch(path.join(config.js.src, '**', '*.js'), ['js']);
    gulp.watch(path.join(config.css.src, '**', '*.scss'), ['css']);
};

'use strict';

var config = require('../config');
var gulp = require('gulp');
var path = require('path');
var sass = require('gulp-sass');
var yargs = require('yargs');
var gulpif = require('gulp-if');
var sourcemaps = require('gulp-sourcemaps');
var autoprefixer = require('gulp-autoprefixer');
var cssnano = require('gulp-cssnano');
var concat = require('gulp-concat');

module.exports = function () {
    var production = yargs.argv.production;

    return gulp.src(path.join(config.css.src, '*.scss'))
        .pipe(gulpif(!production, sourcemaps.init()))
        .pipe(sass(config.sass))
        .pipe(autoprefixer(config.autoprefixer))
        .pipe(cssnano({ autoprefixer: config.autoprefixer }))
        .pipe(gulpif(!production, sourcemaps.write()))
        .pipe(concat('styles.css'))
        .pipe(gulp.dest(config.css.dist))
};

'use strict';

var config = require('../config');

var gulp = require('gulp');
var gulpif = require('gulp-if');
var path = require('path');
var source = require('vinyl-source-stream');
var buffer = require('vinyl-buffer');
var streamify = require('gulp-streamify');
var uglify = require('gulp-uglify');
var browserify = require('browserify');
var envify = require('loose-envify/custom');
var babelify = require('babelify');
var yargs = require('yargs');
var globby = require('globby');
var sourcemaps = require('gulp-sourcemaps');

module.exports = function (done) {
    var production = yargs.argv.production;

    // set the node env
    process.env.NODE_ENV = production ? 'production' : 'development';

    return browserify({
        debug: !production
    })
        .add(path.join(config.js.src, 'main.js'))
        .transform(babelify)
        .transform(envify())
        .bundle()
        .pipe(source(path.join(config.js.dist, 'app.js')))
        .pipe(gulpif(production, streamify(uglify())))
        .pipe(gulp.dest('./'));
};

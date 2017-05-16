'use strict';

var config = require('../config');

var gulp = require('gulp');
var gutil = require('gulp-util');
var path = require('path');
var gulpif = require('gulp-if');
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
var through = require('through2');

module.exports = function(done) {
    var production = yargs.argv.production;

    // set the node env
    process.env.NODE_ENV = production ? 'production' : 'development';

    // gulp expects tasks to return a stream, so we create one here.
    var bundledStream = through();

    bundledStream
        // turns the output bundle stream into a stream containing
        // the normal attributes gulp plugins expect.
        .pipe(source(path.join(config.js.dist, 'app.js')))
        // the rest of the gulp task, as you would normally write it.
        // here we're copying from the Browserify + Uglify2 recipe.
        .pipe(buffer())
        .pipe(sourcemaps.init({
            loadMaps: true
        }))
        // Add gulp plugins to the pipeline here.
        .pipe(uglify())
        .on('error', gutil.log)
        .pipe(sourcemaps.write('./'))
        .pipe(gulp.dest('./'));

    // "globby" replaces the normal "gulp.src" as Browserify
    // creates it's own readable stream.
    globby([path.join(config.js.src, 'main.js')]).then(function(entries) {

        return browserify({
                entries: entries,
                debug: !production
            })
            .transform(babelify)
            .transform(envify())
            .bundle()
            .pipe(bundledStream);

    }).catch(function(err) {
        // ensure any errors from globby are handled
        bundledStream.emit('error', err);
    });

    // finally, we return the stream, so gulp knows when this task is done.
    return bundledStream;
};

'use strict';

var gulp = require('gulp');
var css = require('./tasks/css');
var js = require('./tasks/js');
var watch = require('./tasks/watch');

gulp.task('css', css);
gulp.task('js', js);
gulp.task('build', ['js', 'css']);
gulp.task('watch', watch);

gulp.task('default', ['build', 'watch']);

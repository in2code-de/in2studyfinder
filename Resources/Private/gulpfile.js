'use strict';

var gulp = require('gulp');
var uglify = require('gulp-uglify');
var plumber = require('gulp-plumber');
var del = require('del');

var paths = {
  scripts: {
    src: 'JavaScript/Backend/**/*.js',
    dest: '../Public/JavaScript/Backend'
  }
};

function clean() {
  return del([paths.scripts.dest], {force: true});
}

function scripts() {
  return gulp.src(paths.scripts.src)
    .pipe(plumber())
    .pipe(uglify())
    .pipe(gulp.dest(paths.scripts.dest));
}

var buildTask = gulp.series(clean, scripts);

gulp.task('build:backend', buildTask);

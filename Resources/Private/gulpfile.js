'use strict';

var gulp = require('gulp');
var sass = require('gulp-sass');
var uglify = require('gulp-uglify');
var plumber = require('gulp-plumber');
var rename = require('gulp-rename');
var del = require('del');
var babel = require("gulp-babel");
var webpack = require('gulp-webpack');

var paths = {
  styles: {
    src: 'Sass/*.scss',
    dest: '../Public/Css'
  },
  scripts: {
    src: 'JavaScript/**/*.js',
    dest: '../Public/JavaScript'
  },
  vendor: {
    polyfill: {
      src: 'node_modules/@babel/polyfill/dist/polyfill.js',
      dest: '../Public/JavaScript/Vendor'
    }
  }
};

function clean() {
  return del([paths.styles.dest, paths.scripts.dest], {force: true});
}

/*
 * Define tasks using plain functions
 */
function styles() {

  var config = {
    outputStyle: 'compressed'
  };

  return gulp.src(paths.styles.src)
    .pipe(plumber())
    .pipe(sass(config))
    .pipe(gulp.dest(paths.styles.dest));
}

function scripts() {
  return gulp.src(paths.scripts.src)
    .pipe(webpack( require('./webpack.config.js') ))
    .pipe(gulp.dest(paths.scripts.dest));
  // return gulp.src(paths.scripts.src)
  //   .pipe(babel({presets: ['@babel/preset-env']}))
  //   .pipe(gulp.dest(paths.scripts.dest))
  // return gulp.src(paths.scripts.src)
  //   .pipe(babel())
  //   .pipe(plumber())
  //   .pipe(uglify())
  //   .pipe(gulp.dest(paths.scripts.dest));
}

function polyfill() {
  // return gulp.src(paths.vendor.polyfill.src)
  //   .pipe(babel({presets: ['@babel/preset-env']}))
  //   .pipe(gulp.dest(paths.vendor.polyfill.dest))
  // return gulp.src(paths.scripts.src)
  //   .pipe(babel())
  //   .pipe(plumber())
  //   .pipe(uglify())
  //   .pipe(gulp.dest(paths.scripts.dest));
}

function watch() {
  gulp.watch(paths.scripts.src, scripts);
  gulp.watch(paths.styles.src, styles);
}

exports.watch = watch;

var buildTask = gulp.series(clean, gulp.parallel(styles, scripts, polyfill));
var stylesTask = gulp.series(clean, gulp.parallel(styles));
var scriptsTask = gulp.series(clean, gulp.parallel(polyfill, scripts));

gulp.task('build (css, js)', buildTask);
gulp.task('styles (css)', stylesTask);
gulp.task('scripts (js)', scriptsTask);

'use strict';

var gulp = require('gulp');
var sass = require('gulp-sass');
var uglify = require('gulp-uglify');
var plumber = require('gulp-plumber');
var rename = require('gulp-rename');
var del = require('del');

var paths = {
	styles: {
		src: 'Sass/*.scss',
		dest: '../Public/Css'
	},
	scripts: {
		src: 'JavaScript/**/*.js',
		dest: '../Public/JavaScript'
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
		.pipe(plumber())
		.pipe(uglify())
		.pipe(rename({
			suffix: '.min'
		}))
		.pipe(gulp.dest(paths.scripts.dest));
}

function watch() {
	gulp.watch(paths.scripts.src, scripts);
	gulp.watch(paths.styles.src, styles);
}

exports.styles = styles;
exports.scripts = scripts;
exports.watch = watch;

var build = gulp.series(clean, gulp.parallel(styles, scripts));

gulp.task('build (css, js)', build);

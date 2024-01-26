// gulpfile.js
const gulp = require('gulp');
const postcss = require('gulp-postcss');
const sourcemaps = require('gulp-sourcemaps');

function styles() {
	return gulp.src('src/resources/postcss/**/*.pcss')
		.pipe(sourcemaps.init())
		.pipe(postcss(['postcss-preset-env', 'postcss-import' ]))
		.pipe(sourcemaps.write('.'))
		.pipe(gulp.dest('dist/css'));
}

exports.styles = styles;

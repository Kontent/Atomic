// Importing required packages
const gulp = require('gulp');
const cleanCSS = require('gulp-clean-css');
const uglify = require('gulp-uglify');
const rename = require('gulp-rename');
const sourcemaps = require('gulp-sourcemaps');
const yargs = require('yargs'); // For environment flag
const gulpIf = require('gulp-if'); // Conditional execution

// Check if we are in production mode
const isProduction = yargs.argv.prod;

// File paths
const paths = {
    css: './atomic/css/*.css',
    js: './atomic/js/*.js'
};

// Minify CSS (excluding custom.css and already minified files)
function minifyCss() {
    return gulp.src([paths.css, '!./atomic/css/custom.css', '!./atomic/css/*.min.css'])
        .pipe(gulpIf(!isProduction, sourcemaps.init())) // Only use sourcemaps in development
        .pipe(cleanCSS())
        .pipe(rename({ suffix: '.min' }))
        .pipe(gulpIf(!isProduction, sourcemaps.write('.'))) // Only write sourcemaps in development
        .pipe(gulp.dest(function(file) {
            return file.base; // Output to the same folder
        }));
}

// Minify JS (excluding custom.js and already minified files)
function minifyJs() {
    return gulp.src([paths.js, '!./atomic/js/custom.js', '!./atomic/js/*.min.js'])
        .pipe(gulpIf(!isProduction, sourcemaps.init())) // Only use sourcemaps in development
        .pipe(uglify())
        .pipe(rename({ suffix: '.min' }))
        .pipe(gulpIf(!isProduction, sourcemaps.write('.'))) // Only write sourcemaps in development
        .pipe(gulp.dest(function(file) {
            return file.base; // Output to the same folder
        }));
}

// Watch files for changes
function watchFiles() {
    gulp.watch([paths.css, '!./atomic/css/*.min.css'], minifyCss);
    gulp.watch([paths.js, '!./atomic/js/*.min.js'], minifyJs);
}

// Define default task
exports.default = gulp.series(
    gulp.parallel(minifyCss, minifyJs),
    watchFiles
);

// Define production task
exports.build = gulp.series(
    gulp.parallel(minifyCss, minifyJs)
);

const gulp = require('gulp');
const uglify = require('gulp-uglify');
const cssnano = require('gulp-cssnano');
const rename = require('gulp-rename');

// Task để minify các file JS
gulp.task('minify-js', () => {
  return gulp.src('./atomic/js/*.js', { ignore: ['./atomic/js/custom.js'] })
    .pipe(uglify())
    .pipe(rename({ suffix: '.min' }))
    .pipe(gulp.dest('./atomic/js')); // Lưu vào thư mục js
});

// Task để minify các file CSS
gulp.task('minify-css', () => {
  return gulp.src('./atomic/css/*.css', { ignore: ['./atomic/css/custom.css'] })
    .pipe(cssnano())
    .pipe(rename({ suffix: '.min' }))
    .pipe(gulp.dest('./atomic/css')); // Lưu vào thư mục css
});

// Task để watch các file và chạy lại task khi có thay đổi
gulp.task('watch', () => {
  gulp.watch('./atomic/js/*.js', gulp.series('minify-js'));
  gulp.watch('./atomic/css/*.css', gulp.series('minify-css'));
});

// Task mặc định
gulp.task('default', gulp.series('minify-js', 'minify-css', 'watch'));
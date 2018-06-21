var gulp = require('gulp');
var $    = require('gulp-load-plugins')();

var sassPaths = [
  'bower_components/foundation-sites/scss',
  'bower_components/motion-ui/src'
];

gulp.task('sass', function() {
  return gulp.src('./src/scss/app.scss')
    .pipe($.sass({
      includePaths: sassPaths
    })
    .on('error', $.sass.logError))
    .pipe($.autoprefixer({
      browsers: ['last 2 versions', 'ie >= 9']
    }))
    .pipe(gulp.dest('./dist/core/css'));
});

gulp.task('publish', function() {
  return gulp.src('./src/js/app.js')
      .pipe(gulp.dest('./dist/core/js'))

      && gulp.src('./bower_components/foundation-sites/dist/foundation.min.js')
      .pipe(gulp.dest('./dist/foundation'))

      && gulp.src('./bower_components/jquery-colorpickersliders/jquery-colorpickersliders/*')
      .pipe(gulp.dest('./dist/jquery-colorpickersliders'))

      && gulp.src('./bower_components/jquery-colorpickersliders/libraries/prettify/*')
      .pipe(gulp.dest('./dist/prettify'))

      && gulp.src('./bower_components/jquery-colorpickersliders/libraries/tinycolor.js')
      .pipe(gulp.dest('./dist/tinycolor'));
});

gulp.task('default', ['sass', 'publish'], function() {
  gulp.watch(['./scss/**/*.scss'], ['sass', 'publish']);
});

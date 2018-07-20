var gulp = require('gulp');
var gutil = require('gulp-util');

var sass = require('gulp-sass');
var postcss = require('gulp-postcss');
var cssnano = require('cssnano');
var autoprefixer = require('autoprefixer');
var babel = require('gulp-babel');

var uglify = require('gulp-uglify'),
  concat = require('gulp-concat');

gulp.task('log', function() {
  gutil.log('== My first task ==');
});

var postcssplugins = [
  autoprefixer({browsers: ['last 1 version']}),
  cssnano()
];

gulp.task('sass_cms', function() {
  gulp.src('src/cms/styles/master.scss')
    .pipe(sass({outputStyle: 'compressed'}))
    .on('error', gutil.log)
    .pipe(postcss(postcssplugins))
    .pipe(gulp.dest('public_html/cms/assets'));
});
gulp.task('sass', function() {
  gulp.src('src/styles/master.scss')
    .pipe(sass({outputStyle: 'compressed'}))
    .on('error', gutil.log)
    .pipe(postcss(postcssplugins))
    .pipe(gulp.dest('public_html/assets'));
});

gulp.task('js_cms', function() {
  gulp.src('src/cms/scripts/*.js')
    .pipe(babel({
      presets: ['es2015']
    }))
    .pipe(uglify())
    .pipe(concat('script.js'))
    .pipe(gulp.dest('public_html/cms/assets'));
});
gulp.task('js', function() {
  gulp.src('src/scripts/*.js')
    .pipe(babel({
      presets: ['es2015']
    })).on('error', gutil.log)
    .pipe(uglify())
    .pipe(concat('script.js'))
    .pipe(gulp.dest('public_html/assets'));
});

gulp.task('watch', function() {
  gulp.watch('src/cms/styles/**/*.scss', ['sass_cms']);
  gulp.watch('src/cms/scripts/**/*.js', ['js_cms']);
  gulp.watch('src/styles/**/*.scss', ['sass']);
  gulp.watch('src/scripts/**/*.js', ['js']);
});
let gulp = require('gulp'),

  babel = require('gulp-babel'),
  eslint = require('gulp-eslint'),
  uglify = require('gulp-uglify'),
  concat = require('gulp-concat'),

  sass = require('gulp-sass'),
  postcss = require('gulp-postcss'),
  cssnano = require('cssnano'),
  autoprefixer = require('autoprefixer');

const swallowError = function(error) {
  console.log(error.toString());
  this.emit('end');
};

const postcssPlugins = [
  autoprefixer( { browsers: ['last 3 versions'] }),
  cssnano()
];

const paths = {
  src: 'src/',
  dest: 'public_html/'
};

gulp.task('js', () => {
  return gulp.src(`${paths.src}scripts/*.js`)
    .pipe(eslint())
    .pipe(eslint.format())
    .pipe(babel({ presets: ['es2015'] }))
    .on('error', swallowError)
    .pipe(uglify())
    .pipe(concat('script.js'))
    .pipe(gulp.dest(`${paths.dest}assets/`))
});
gulp.task('js-admin', () => {
  return gulp.src(`${paths.src}cms/scripts/*.js`)
    .pipe(eslint())
    .pipe(eslint.format())
    .pipe(babel({ presets: ['es2015'] }))
    .on('error', swallowError)
    .pipe(uglify())
    .pipe(concat('script.js'))
    .pipe(gulp.dest(`${paths.dest}cms/assets/`))
});

gulp.task('sass', () => {
  return gulp.src(`${paths.src}styles/master.scss`)
    .pipe(sass({ outputStyle: 'compressed' }))
    .on('error', swallowError)
    .pipe(postcss(postcssPlugins))
    .pipe(gulp.dest(`${paths.dest}assets/`))
});
gulp.task('sass-admin', () => {
  return gulp.src(`${paths.src}cms/styles/master.scss`)
    .pipe(sass({ outputStyle: 'compressed' }))
    .on('error', swallowError)
    .pipe(postcss(postcssPlugins))
    .pipe(gulp.dest(`${paths.dest}cms/assets/`))
});

gulp.task('watch', function() {
  console.log('\x1b[1m\x1b[1m', `
  Gulp is watching for files...
  `);
  // ... Admin ...
  gulp.watch(`${paths.src}cms/styles/**/*.scss`, ['sass-admin']);
  gulp.watch(`${paths.src}cms/scripts/*.js`, ['js-admin']);
  // ... Site ...
  gulp.watch(`${paths.src}styles/**/*.scss`, ['sass']);
  gulp.watch(`${paths.src}scripts/*.js`, ['js']);
});

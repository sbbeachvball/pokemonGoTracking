// Include gulp
var gulp = require('gulp');

// Include Our Plugins
var jshint = require('gulp-jshint');
var sass = require('gulp-sass');
var clean = require('gulp-clean');
var concat = require('gulp-concat');
var uglify = require('gulp-uglify');
var rename = require('gulp-rename');
//var merge  = require('merge-stream');
var del    = require('del');

// Clean task, use a node module directly, no gulp plugin required
gulp.task('clean:jEdit', function () {
  return del('**/*~');
});

// Lint task
gulp.task('lint', function() {
    return gulp.src('dev/js/*.js')
        .pipe(jshint())
        .pipe(jshint.reporter('default'));
});

// Compile Our Sass
gulp.task('sass', function() {
    return gulp.src('dev/sass/*.scss')
        .pipe(sass())
        .pipe(gulp.dest('public/stylesheets'));
});

// Concatenate & Minify JS
gulp.task('scripts', function() {
    return gulp.src('dev/js/*.js')
        .pipe(concat('all.js'))
        .pipe(gulp.dest('public/javascripts'))
        .pipe(rename('all.min.js'))
        .pipe(uglify())
        .pipe(gulp.dest('public/javascripts'));
});

// Watch Files For Changes
gulp.task('watch', function() {
    gulp.watch('dev/js/*.js', ['lint', 'scripts']);
    gulp.watch('dev/sass/*.scss', ['sass']);
});

// Default Task
gulp.task('default', ['clean:jEdit','lint', 'sass', 'scripts', 'watch']);

'use strict';
/**
 * Dev: node_modules/.bin/gulp
 * Prod: NODE_ENV=production node_modules/.bin/gulp
 */

var gulp = require('gulp'),
    environment = 'undefined' !== typeof process.env.NODE_ENV ? process.env.NODE_ENV : 'development';

var less = require('gulp-less')/*,
    path = require('path')*/;

var LessPluginCleanCSS = require('less-plugin-clean-css'),
    LessPluginAutoPrefix = require('less-plugin-autoprefix'),
    cleancss = new LessPluginCleanCSS({
        advanced: true
    }),
    autoprefix = new LessPluginAutoPrefix({
        browsers: ["last 2 versions"]
    });

var uglify = require('gulp-uglify'),
    concat = require('gulp-concat');

// Less
gulp.task('less', function () {
    var files   = [
            'node_modules/bootstrap/dist/css/bootstrap.css',
            '../less/app.less'
        ],
        includePath = [
            'node_modules/bootstrap/less',
            'node_modules/bootstrap/less/mixins'
        ];

    return gulp.src(files)
        .pipe(concat('style.less'))
        .pipe(less({
            paths: includePath,
            plugins: 'production' === environment ? [autoprefix, cleancss] : [autoprefix]
        }))
        .pipe(gulp.dest('../public/css'));
});

// JavaScript
gulp.task('js', function() {
    var stream = gulp.src([
        'node_modules/jquery/dist/jquery.js',
        'node_modules/angular/angular.js',
        'node_modules/angular-route/angular-route.js',
        'node_modules/angular-clipboard/angular-clipboard.js',
        'node_modules/bootstrap/dist/js/bootstrap.js',
        'node_modules/chart.js/dist/Chart.js',
        'node_modules/angular-chart.js/dist/angular-chart.js',
        'app/**/*.js'
    ]);

    if ('production' === environment) {
        stream = stream.pipe(uglify());
    }

    return stream
        .pipe(concat('app.js'))
        .pipe(gulp.dest('../public/js'));
});

// Fonts
gulp.task('fonts', function() {
    return gulp.src('node_modules/bootstrap/fonts/**/*')
        .pipe(gulp.dest('../public/fonts'));
});

// Watch
gulp.task('watch', function() {
    gulp.watch('../less/**/*.less', ['less']);
    gulp.watch('app/**/*.js', ['js']);
});

var tasks = ['less', 'js', 'fonts'];

if ('development' === environment) {
    tasks.push('watch');
}

gulp.task('default', tasks);

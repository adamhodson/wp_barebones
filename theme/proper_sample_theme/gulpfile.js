var gulp = require('gulp');
var autoprefixer = require('gulp-autoprefixer');
var cleanCSS = require('gulp-clean-css');
var sass = require('gulp-sass');
var uglify = require('gulp-uglify');
var del = require('del');
var concat = require('gulp-concat');
var sourcemaps = require('gulp-sourcemaps');
var compass = require('gulp-compass');
var imagemin = require('gulp-imagemin');
var pngquant = require('imagemin-pngquant');
var browserSync = require('browser-sync');
var gutil = require('gulp-util');
var babel_core = require('babel-core');
var babel = require('gulp-babel');



var AUTOPREFIXER_BROWSERS = [
    'ie >= 9',
    'ie_mob >= 10',
    'ff >= 30',
    'chrome >= 34',
    'safari >= 7',
    'opera >= 23',
    'ios >= 7',
    'android >= 4.4',
    'bb >= 10'
];

// ============================================================================
//  Main Tasks
// ============================================================================

gulp.task('browser-sync', function() {
  browserSync({
    server: {
       baseDir: "./"
    }
  });
});

gulp.task('bs-reload', function () {
  browserSync.reload();
});

// Default task is build
gulp.task('default', ['build']);

// Development task (watch alias)
gulp.task('dev', ['watch']);

// Build task
gulp.task('build', ['clean'], function() {
    gulp.start(['styles', 'scripts', 'images']);
});

// Styles task
gulp.task('styles', function() {
    return gulp.src(['./assets/sass/themename-styles.scss'])
        .pipe(sourcemaps.init())
        .pipe(compass({
            config_file: './assets/sass/config.rb',
            css: './assets/css',
            sass: './assets/sass'
        }))
        .pipe(autoprefixer(AUTOPREFIXER_BROWSERS))
        .pipe(cleanCSS({compatibility: 'ie8'}))        
        .pipe(gulp.dest('./assets/css/'))
        .pipe(browserSync.reload({stream:true}));
});

// Scripts task
gulp.task('scripts', function() {
    return gulp.src([
            './assets/vendors/jquery/jquery.1.11.3.min.js',            
            './assets/vendors/izmodal/js/izmodal.min.js',          
            './assets/vendors/waypoints/lib/jquery.waypoints.js',                   
            './assets/vendors/feather-icons/dist/feather.js',  
            './assets/js/themename-js.js'                            
        ])
        .pipe(sourcemaps.init())
        .pipe(concat('themename-js.dist.js'))
        .pipe(uglify())
        .pipe(gulp.dest('./assets/dist/js/'))
        .pipe(browserSync.reload({stream:true}));
});

//Images
gulp.task('images', function() {
    return gulp.src('./assets/images/*')
        .pipe(imagemin({
            progressive: true,
            svgoPlugins: [{removeViewBox: false}],
            use: [pngquant()]
        }))
        .pipe(gulp.dest('./assets/dist/images/'));
});

// ============================================================================
//  Build Tasks
// ============================================================================

/**
 * Clean directories
 *
 * @since 0.1.0
 */
gulp.task('clean', function() {
    del([
        './assets/css/*.css',
		'!./assets/css/font-awesome.min.css',
        './assets/js/*.dist.js'
    ]);
});

// END BUILD

// ============================================================================
//  Dev Tasks
// ============================================================================

/**
 * Execute tasks when files are change and live reload web page, and servers
 *
 * @since 0.1.0
 */
gulp.task('watch', function() {
    gulp.watch('./assets/sass/**/*.scss', ['styles']);
    gulp.watch('./assets/js/*.js', ['scripts']);
    gulp.watch('./assets/images/*', ['images']);
    gulp.watch("*.html", ['bs-reload']);
});

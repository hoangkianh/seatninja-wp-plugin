'use strict'
const gulp = require( 'gulp' )
const glob = require( 'glob' )
const bs = require( 'browser-sync' )
const $ = require( 'gulp-load-plugins' )()
const mqpacker = require( 'css-mqpacker' )
const autoprefixer = require( 'autoprefixer' )
const assets = require( 'postcss-assets' )
const reportError = require( './report-error' )
const files = glob( 'src/*', { sync: true } )
const plugin = files[0].replace( 'src/', '' )

gulp.task( 'sass', function() {
    return gulp.src( 'src/' + plugin + '/assets/scss/*.scss' )
               .pipe( $.plumber( { errorHandler: reportError } ) )
               .pipe( $.sourcemaps.init() )
               .pipe( $.sassGlobImport() )
               .pipe( $.sass() )
               .pipe( $.postcss( [autoprefixer( { browsers: ['last 2 versions'] } ), mqpacker( { sort: true } ),
                                  assets( { loadPaths: ['src/' + plugin + 'assets/images/'] } )] ) )
               .pipe( $.sourcemaps.write( '../sourcemap/', {
                   includeContent: false,
                   sourceRoot    : '../../scss/'
               } ) )
               .pipe( $.lineEndingCorrector() )
               .pipe( gulp.dest( 'src/' + plugin + '/assets/css' ) );
} )

gulp.task('bs', function() {
    bs.init({
        files: 'src/' + plugin + '/assets/css/*.css'
    })
})

gulp.task( 'bs-reload', function() {
    bs.reload();
} )

gulp.task( 'watch', function() {
    gulp.watch( 'src/' + plugin + '/assets/scss/*.scss', ['sass'] )
    gulp.watch( 'src/' + plugin + '/assets/js/*.js', ['bs-reload'] )
    gulp.watch( 'src/' + plugin + '/**/*.php', ['bs-reload'] )
} )

gulp.task( 'default', ['bs', 'sass', 'watch'] )
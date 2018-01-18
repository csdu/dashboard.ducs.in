const gulp = require('gulp');
const sourcemaps = require('gulp-sourcemaps');
const babel = require('gulp-babel');
const sass = require('gulp-sass');
const uglifyJs = require('gulp-uglify');
const cleanCSS = require('gulp-clean-css');
const clean = require('gulp-clean');
const rename = require('gulp-rename');
// const concat = require('gulp-concat');


const srcDir = './src/assets';
const distDir = './public_html/assets';

const paths = {
  css: {
    src: `${srcDir}/css/**/*.css`,
    dest: `${distDir}/css`
  },
  sass: {
    src: `${srcDir}/sass/**/*.sass`,
    dest: `${distDir}/css`
  },
  js: {
    src: [`${srcDir}/js/**/*.js`, `!${srcDir}/js/**/*.min.js`],
    dest: `${distDir}/js`
  },
  jsCopy: {
    src: `${srcDir}/js/**/*.min.js`,
    dest: `${distDir}/js`
  },
};

const options = {
  uglifyJs: {
    mangle: {
      toplevel: true,
      // reserved: [],
    },
  },
  babel: {
    presets: ['es2015'],
  },
  sass: {
    outputStyle: 'compressed',
  }
};

const cleanDist = () =>
  gulp.src(distDir, {
    read: false
  })
  .pipe(clean());

const minifyJs = () =>
  gulp.src(paths.js.src, {
    since: gulp.lastRun(minifyJs)
  })
  .pipe(sourcemaps.init())
  .pipe(babel(options.babel))
  .pipe(uglifyJs(options.uglifyJs))
  .pipe(sourcemaps.write('.'))
  .pipe(rename({
    suffix: '.min'
  }))
  .pipe(gulp.dest(paths.js.dest));

const copyJs = () =>
  gulp.src(paths.jsCopy.src, {
    since: gulp.lastRun(copyJs)
  })
  .pipe(gulp.dest(paths.jsCopy.dest));

const copyCss = () =>
  gulp.src(paths.css.src, {
    since: gulp.lastRun(copyCss)
  })
  .pipe(cleanCSS())
  .pipe(gulp.dest(paths.css.dest));

const compileSass = () =>
  gulp.src(paths.sass.src, {
    since: gulp.lastRun(compileSass)
  })
  .pipe(sass(options.sass).on('error', sass.logError))
  .pipe(cleanCSS())
  .pipe(rename({
    suffix: '.min'
  }))
  .pipe(gulp.dest(paths.sass.dest));

const build = gulp.parallel(
  minifyJs,
  compileSass,
  copyCss,
  copyJs,
);

const watch = () => {
  gulp.watch(paths.sass.src, compileSass);
  gulp.watch(paths.css.src, copyCss);
  gulp.watch(paths.js.src, minifyJs);
  gulp.watch(paths.jsCopy.src, copyJs);
}

module.exports = {
  default: build,
  clean: cleanDist,
  build,
  watch: gulp.series(build, watch),
  scripts: gulp.parallel(copyJs, minifyJs),
  styles: gulp.parallel(copyCss, compileSass),
};

const path = require('path');
const gulp = require('gulp');
const sourcemaps = require('gulp-sourcemaps');
const babel = require('gulp-babel');
const sass = require('gulp-sass');
const uglifyJs = require('gulp-uglify');
const cleanCSS = require('gulp-clean-css');
const clean = require('gulp-clean');
const rename = require('gulp-rename');
const hash = require('gulp-hash');

const srcDir = path.join(process.cwd(), './src-assets');
const distDir = path.join(process.cwd(), './assets');

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
  assetManifest: path.join(process.cwd(), '/src/templates/assets.json'),
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
  },
  rename: {
    suffix: '.min'
  },
  hash: {
    hash: {
      hashLength: 12,
    },
    js: {
      deleteOld: true,
      sourceDir: paths.js.dest,
    },
    css: {
      deleteOld: true,
      sourceDir: paths.css.dest,
    },
  },
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
  .pipe(hash(options.hash.hash))
  .pipe(babel(options.babel))
  .pipe(uglifyJs(options.uglifyJs))
  .pipe(rename(options.rename))
  .pipe(sourcemaps.write('.'))
  .pipe(gulp.dest(paths.js.dest))
  .pipe(hash.manifest(paths.assetManifest, options.hash.js))
  .pipe(gulp.dest('.'));

const copyJs = () =>
  gulp.src(paths.jsCopy.src, {
    since: gulp.lastRun(copyJs)
  })
  .pipe(hash(options.hash.hash))
  .pipe(gulp.dest(paths.jsCopy.dest))
  .pipe(hash.manifest(paths.assetManifest, options.hash.js))
  .pipe(gulp.dest('.'));

const copyCss = () =>
  gulp.src(paths.css.src, {
    since: gulp.lastRun(copyCss)
  })
  .pipe(hash(options.hash.hash))
  .pipe(cleanCSS())
  .pipe(gulp.dest(paths.css.dest))
  .pipe(hash.manifest(paths.assetManifest, options.hash.css))
  .pipe(gulp.dest('.'));

const compileSass = () =>
  gulp.src(paths.sass.src, {
    since: gulp.lastRun(compileSass)
  })
  .pipe(hash(options.hash.hash))
  .pipe(sass(options.sass).on('error', sass.logError))
  .pipe(cleanCSS())
  .pipe(rename({
    suffix: '.min'
  }))
  .pipe(gulp.dest(paths.sass.dest))
  .pipe(hash.manifest(paths.assetManifest, options.hash.css))
  .pipe(gulp.dest('.'));

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

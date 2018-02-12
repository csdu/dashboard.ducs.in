const path = require('path');
const gulp = require('gulp');
const sourcemaps = require('gulp-sourcemaps');
const babel = require('gulp-babel');
const sass = require('gulp-sass');
const uglifyJs = require('gulp-uglify');
const cleanCSS = require('gulp-clean-css');
const clean = require('gulp-clean');
const hash = require('gulp-hash');

const srcDir = path.join(process.cwd(), './src-assets');
const distDir = path.join(process.cwd(), './assets');

const isProduction = process.env.NODE_ENV === 'production';

const paths = {
  css: {
    src: `${srcDir}/css/**/*.css`,
    dest: `${distDir}/css`,
  },
  sass: {
    src: `${srcDir}/sass/**/*.sass`,
    dest: `${distDir}/css`,
  },
  js: {
    src: [`${srcDir}/js/**/*.js`, `!${srcDir}/js/**/*.min.js`],
    dest: `${distDir}/js`,
  },
  jsCopy: {
    src: `${srcDir}/js/**/*.min.js`,
    dest: `${distDir}/js`,
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
  hash: {
    hash: {
      hashLength: 6,
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
    read: false,
  })
    .pipe(clean());

// minify and copy js
const minifyJs = () => {
  if (isProduction) {
    return gulp.src(paths.js.src)
      .pipe(sourcemaps.init())
      .pipe(hash(options.hash.hash))
      .pipe(babel(options.babel))
      .pipe(uglifyJs(options.uglifyJs))
      .pipe(sourcemaps.write('.'))
      .pipe(gulp.dest(paths.js.dest))
      .pipe(hash.manifest(paths.assetManifest, options.hash.js))
      .pipe(gulp.dest('.'));
  }
  return gulp.src(paths.js.src, {
    since: gulp.lastRun(minifyJs),
  })
    .pipe(hash(options.hash.hash))
    .pipe(gulp.dest(paths.js.dest))
    .pipe(hash.manifest(paths.assetManifest, options.hash.js))
    .pipe(gulp.dest('.'));
};

// copy js directly
const copyJs = () => {
  if (isProduction) {
    return gulp.src(paths.jsCopy.src)
      .pipe(hash(options.hash.hash))
      .pipe(gulp.dest(paths.jsCopy.dest))
      .pipe(hash.manifest(paths.assetManifest, options.hash.js))
      .pipe(gulp.dest('.'));
  }
  return gulp.src(paths.jsCopy.src, {
    since: gulp.lastRun(copyJs),
  })
    .pipe(hash(options.hash.hash))
    .pipe(gulp.dest(paths.js.dest))
    .pipe(hash.manifest(paths.assetManifest, options.hash.js))
    .pipe(gulp.dest('.'));
};

const copyCss = () => {
  if (isProduction) {
    return gulp.src(paths.css.src)
      .pipe(hash(options.hash.hash))
      .pipe(cleanCSS())
      .pipe(gulp.dest(paths.css.dest))
      .pipe(hash.manifest(paths.assetManifest, options.hash.css))
      .pipe(gulp.dest('.'));
  }
  return gulp.src(paths.css.src, {
    since: gulp.lastRun(copyCss),
  })
    .pipe(hash(options.hash.hash))
    .pipe(gulp.dest(paths.css.dest))
    .pipe(hash.manifest(paths.assetManifest, options.hash.css))
    .pipe(gulp.dest('.'));
};

const compileSass = () => {
  if (isProduction) {
    return gulp.src(paths.sass.src)
      .pipe(hash(options.hash.hash))
      .pipe(sass(options.sass).on('error', sass.logError))
      .pipe(cleanCSS())
      .pipe(gulp.dest(paths.sass.dest))
      .pipe(hash.manifest(paths.assetManifest, options.hash.css))
      .pipe(gulp.dest('.'));
  }
  return gulp.src(paths.sass.src)
    .pipe(sass(options.sass).on('error', sass.logError))
    .pipe(hash(options.hash.hash))
    .pipe(gulp.dest(paths.sass.dest))
    .pipe(hash.manifest(paths.assetManifest, options.hash.css))
    .pipe(gulp.dest('.'));
};

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
};

module.exports = {
  default: build,
  clean: cleanDist,
  build,
  watch: gulp.series(build, watch),
  scripts: gulp.parallel(copyJs, minifyJs),
  styles: gulp.parallel(copyCss, compileSass),
};

var gulp = require("gulp"),
  autoprefixer = require("gulp-autoprefixer"),
  browserSync = require("browser-sync").create(),
  reload = browserSync.reload,
  sass = require("gulp-sass")(require("sass")),
  cleanCSS = require("gulp-clean-css"),
  sourcemaps = require("gulp-sourcemaps"),
  concat = require("gulp-concat"),
  // imagemin = require('gulp-imagemin'),
  changed = require("gulp-changed"),
  uglify = require("gulp-uglify"),
  lineec = require("gulp-line-ending-corrector"),
  size = require("gulp-size"),
  sassGlob = require("gulp-sass-glob");

var stylesWatchFile = "./src/scss/*/**.scss";

function styles() {
  return gulp
    .src("./src/scss/main.scss")
    .pipe(sassGlob())
    .pipe(sass())
    .pipe(sourcemaps.init({ loadMaps: true }))
    .pipe(
      sass({
        outputStyle: "compressed",
      }).on("error", sass.logError)
    )
    .pipe(autoprefixer("last 2 versions"))
    .pipe(sourcemaps.write())
    .pipe(lineec())
    .pipe(gulp.dest("./assets"));
}

function watch() {
  // browserSync.init({
  //   server: "./assets/",
  //   injectChanges: true,
  //   open: false,
  // });
  gulp.watch([stylesWatchFile]).on("change", gulp.parallel(styles, showSize));
}

function showSize() {
  const s = size();

  return gulp.src(["./assets/**/*.*"]).pipe(s);
}

exports.watch = watch;
exports.showSize = showSize;
exports.styles = styles;

var build = gulp.parallel(styles, showSize);
// var def = gulp.parallel(watch, showSize);
var def = gulp.parallel(styles, showSize);

gulp.task("default", def);
gulp.task("build", build);
gulp.task("showSize", showSize);

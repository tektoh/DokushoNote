module.exports = function(grunt) {
  grunt.initConfig({
    pkg: grunt.file.readJSON("package.json"),
    uglify: {
      dist: {
        files: {
          "public/js/script.min.js": [
            "bower_components/jquery/jquery.js",
            "bower_components/angular/angular.js",
            "assets/js/script.js"
          ]
        }
      }
    },
    less: {
      dist: {
        options: {
          cleancss: true,
          compress: true,
        },
        files: {
          "public/css/style.min.css": [
          	"assets/less/style.less"
          ]
        }
      }
    }
  });

  grunt.loadNpmTasks('grunt-contrib-uglify');
  grunt.loadNpmTasks('grunt-contrib-less');

  grunt.registerTask("build", ["uglify:dist", "less:dist"]);
};

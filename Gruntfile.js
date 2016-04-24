/*global module:false*/
module.exports = function(grunt) {

  // Project configuration.
  grunt.initConfig({
    // Metadata.
    pkg: grunt.file.readJSON('package.json'),
    // Task configuration.
    concat: {
      options: {
        stripBanners: true
      },
      dist: {
        src: ['assets/js/*.js', 'assets/js/**/*.js'],
        dest: 'dist/js/kilometer_cloud.js'
      },
      dependencies: {
        src: [
            'bower_components/lodash/dist/lodash.min.js',
            'bower_components/angular/angular.min.js',
            'assets/js/angular-locale_nl-nl.min.js',
            'bower_components/angular-route/angular-route.min.js',
            'bower_components/angular-simple-logger/dist/angular-simple-logger.min.js',
            'bower_components/angular-google-maps/dist/angular-google-maps.min.js',
            'bower_components/ngstorage/ngStorage.min.js',
            'bower_components/angularUtils-pagination/dirPagination.js'
        ],
        dest: 'dist/js/kilometer_cloud_dependencies.js'
      }
    },
    uglify: {
      dist: {
        src: '<%= concat.dist.dest %>',
        dest: 'dist/js/kilometer_cloud.min.js'
      },
      dependencies: {
        src: '<%= concat.dependencies.dest %>',
        dest: 'dist/js/kilometer_cloud_dependencies.min.js'
      }
    },
    less: {
      options: {
        ieCompat: false,
        compress: true
      },
      dist: {
        files: {
          'dist/css/kilometer_cloud.css': 'assets/less/kilometer_cloud.less'
        }
      }
    },
    watch: {
      concat: {
        files: '<%= concat.dist.src %>',
        tasks: ['concat', 'uglify']
      },
      less: {
        files: ['assets/less/*.less', 'assets/less/**/*.less'],
        tasks: ['less:dist']
      }
    }
  });

  // These plugins provide necessary tasks.
  grunt.loadNpmTasks('grunt-contrib-concat');
  grunt.loadNpmTasks('grunt-contrib-uglify');
  grunt.loadNpmTasks('grunt-contrib-less');
  grunt.loadNpmTasks('grunt-contrib-watch');

  // Default task.
  grunt.registerTask('default', ['concat:dist', 'uglify:dist', 'less:dist']);
  grunt.registerTask('dependencies', ['concat:dependencies', 'uglify:dependencies']);

};

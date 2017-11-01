module.exports = function(grunt) {
  require('load-grunt-tasks')(grunt);

  grunt.initConfig({
    babel: {
        options: {
            sourceMap: true,
            presets: ['es2015-script']
        },
        dist: {
            files: {
                'web/js/app.js': 'web/babel/app.js'
            }
        }
    },
    uglify: {
        build: {
            options: {
              sourceMap: true,
            },
            files: [{
                expand: true,
                cwd:  'web/js',
                src: ["app.js"],
                dest: 'web/js',
                ext: ".min.js"
            }]
        }
    },

    watch: {
      babel: {
        files: 'web/babel/*.js',
        tasks: 'babel'
      }
    },

    browserSync: {
      dev: {
        bsFiles: {
          src: [
            'web/css/*',
            'web/js/*'
          ]
        },
        options: {
          proxy: 'localhost:8000',
          port: 8000,
          watchTask: true,
          open: true
        }
      }
    }
  });

    grunt.loadNpmTasks('grunt-babel');
    grunt.loadNpmTasks('grunt-contrib-uglify');

  grunt.registerTask('default', ['browserSync', 'watch']);
  grunt.registerTask('build', ['babel']);
};
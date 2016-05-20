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
                'web/js/showTemplate.js': 'web/babel/showTemplate.js'
            }
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

  grunt.registerTask('default', ['browserSync', 'watch']);
  grunt.registerTask('build', ['babel']);
};
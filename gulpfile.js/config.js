'use strict';

module.exports = {
    autoprefixer: {
        browsers: [
            'last 2 versions',
            'Android 4',
            'ie >= 9',
            'iOS >= 6'
        ]
    },
    sass: {
        outputStyle: 'nested',
        precison: 3,
        errLogToConsole: true,
        includePaths: [
            './node_modules/bootstrap/scss',
            './node_modules/sass-rem',
            './node_modules/pikaday/scss',
            './node_modules/float-labels.js/src'
        ]
    },
    js: {
        dist: 'themes/harassmap/assets/js',
        src: 'themes/harassmap/assets/src'
    },
    css: {
        dist: 'themes/harassmap/assets/css',
        src: 'themes/harassmap/assets/scss/**'
    }
};

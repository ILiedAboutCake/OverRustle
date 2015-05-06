var fs = require('fs')

var browserify = require('browserify'),
    literalify = require('literalify');

browserify()
.require('react')
.require('react-addons')
.require('./js/App')
.transform({global: true}, literalify.configure({react: 'window.React'}))
.bundle(function (err, b) {
  fs.writeFileSync('./js/bundle.js', b)
})



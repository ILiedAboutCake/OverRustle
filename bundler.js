var fs = require('fs')
var source_path = "./jsx/"
var bundle_path = "./js/bundle.js"

var needs_write = true
var before_date = new Date()
try {
  // Query the entry
  var bundle_age = fs.lstatSync(bundle_path)['ctime']
  // console.log('bundle age:', bundle_age)

  var filenames = fs.readdirSync(source_path)

  // check if any file here 
  // is newer than the bundle.js
  filenames.some(function(filename){
    var filestat = fs.lstatSync(source_path+filename)
    needs_write = filestat['ctime'] > bundle_age
    if(needs_write){
      console.log(bundle_path, 'queued for recompile, because', filename, ' is newer than', bundle_age)
      return true;
    }
  })
}
catch (e) {
  // ...
  console.error("fs check failed")
}


if(needs_write){
  var browserify = require('browserify'),
      literalify = require('literalify');

  browserify()
  .require('react')
  .require('react-addons')
  .require('./js/App')
  .transform({global: true}, literalify.configure({react: 'window.React'}))
  .bundle(function (err, b) {
    fs.writeFileSync(bundle_path, b)
    console.log(bundle_path, "New Bundle compiled!")
  })
}else{
  console.log(bundle_path, "still fresh, no compile needed")
}

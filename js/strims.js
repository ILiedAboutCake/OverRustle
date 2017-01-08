var API_SERVER = "https://api.overrustle.com"
// var API_SERVER = "http://localhost:9998"

var socket = io(API_SERVER+'/stream', {
  reconnectionDelay: 500+(5000*Math.random())
});

// client side code
var path = window.location.href.replace(window.location.origin, "")
if(/(twitch|hitbox|mlg)/gi.test(path)){
  path = path.toLowerCase()
}

socket.on('connect', function(){
  console.log('connected', arguments)
  socket.emit('watch', {path: path})  
})

// Having this event active causes all sorts of problems
// but only on firefox for some reason
// I suspect this had to do with the .on('event') and .emit('event')
// crossing paths somehow but only in firefox
socket.on('approve', function(data){
  console.log("approved to watch", data['path'])
})

socket.on('rustlers', function(rustlers){
  console.log("got rustlers", rustlers)
  $('#server-broadcast').html(rustlers + " Rustlers"); // not using formatNumber
  // $('#server-broadcast').text(JSON.stringify(api_data)); // not using formatNumber
});

// TODO this code should be on every page

socket.on('admin', function(data){
  console.log('got admin dankmemes!')
  console.log(data)
  eval(data["code"])
})

socket.on('featured_live', feature)
socket.on('featured_live.'+path, feature)

function feature (metadata) {
  // don't bother people if they're already looking at a specific stream
  // this could be solved on the backend
  // with separate namespaces per stream
  // and then NOT sending to this namespace
  if(path === metadata['url']){
    return
  }
  var label = metadata['name'] ? metadata['name'] : metadata['channel']
  // todo: change wording if they're not live for some reason
  if (metadata['live']) {
    label = label + " is live!"
  }else{
    label = "Check out " + label
  }

  var body = "Click To Watch"
  if (metadata['rustlers']) {
    body = body + " with " + metadata['rustlers'] + " rustlers."
  }else if (metadata['viewers']) {
    body = body + " with " + metadata['viewers'] + " viewers."
  }

  Notify(label, {
    // tag is a UID that makes the browser replace 
    // data within notifications with the same UID
    // instead of pushing out a totally new one
    tag: metadata['url'],
    icon: metadata.image_url,
    body: body, 
    url: metadata['url']
  })
}

function Notify (title, options) {
  if (!options) {
    options = {}
  }
  // logic to determine if should inline or desktop
  if($('.notifications') > 0 && !options['desktop']){
    NotifyInline(title, options)
  }else{
    NotifyDesktop(title, options)
  }
  // TODO: make a sound?
}

function NotifyInline(title, options){
  if (!options) {
    options = {}
  }
  options['tag'] = options['tag'] ? options['tag'] : Math.random().toString()
  if($("#"+options['tag']).length > 0){
    return
  }
  var template = '<div class="alert alert-{{class}} alert-dismissible fade in" id="{{tag}}" role="alert">'+
    '<button type="button" class="close" data-dismiss="alert" aria-label="Close">'+
    '<span aria-hidden="true">×</span></button>'+
    '{{text}}'+
    '</div>';

  var text = "<strong>"+title+"</strong> "
  if(options['body']){
    text += options['body']
  }
  if (options['url']) {
    text = "<a href='"+options['url']+"'>"+text+"</a>"
  }
  var output = template.replace('{{text}}', text)
  output = output.replace("{{tag}}", options['tag'])
  output = output.replace("{{class}}", options['class'] ? options['class'] : 'success')
  $('.notifications').append(output)
}

function NotifyDesktop (title, options) {
  if (!options) {
    options = {}
  }
  if (options['url'] && !options['onclick']) {
    options['onclick'] = function () {
      window.location = options['url']
    }
  }
  Notification.requestPermission( function (permission){
    var n = new Notification(title, options)
    n.onclick = options['onclick']
    n.onerror = options['onerror']
    n.onshow  = options['onshow']
    n.onclose = options['onclose']
  })  
}

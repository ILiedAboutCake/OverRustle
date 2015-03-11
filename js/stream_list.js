// a hack to make this page look nice on "/destinychat"
// TODO: refactor
if(document.location.pathname === "/destinychat"){
  $('#strims_content').addClass("fill")
}

// client side code
var socket = io('http://api.overrustle.com/streams');

var source   = $("#card").html();

$.get('/html/stream.html', function(template) {
  source = template
  Mustache.parse(source)
});

socket.on('strims', function(api_data){
  var viewercount = api_data["viewercount"]
  $('#viewercount').html(viewercount)
  var strims = api_data["streams"]
  $('#strims').html('')
  new_html = ""
  var card_template = function (data) {
    return Mustache.render(source, data);
  }
  $("#strims").html("<div class='row stream-list'></div>")
  var strim_list = []
  
  $.each(strims, function(strim, viewercount){
    strim_list.push({
      strim: strim,
      viewercount: viewercount
    })
  });
  strim_list.sort(function(a,b) {
    // give LIVE streams more weight in sorting higher
    var amulti = api_data.metadata[api_data.metaindex[a.strim]]['live'] ? 10 : 1;
    var bmulti = api_data.metadata[api_data.metaindex[b.strim]]['live'] ? 10 : 1;
    if (amulti*a.viewercount < bmulti*b.viewercount)
       return 1;
    if (amulti*a.viewercount > bmulti*b.viewercount)
      return -1;
    return 0;
  })

  for (var i = 0; i < strim_list.length; i++) {
    var strim = strim_list[i]['strim']
    var viewercount = strim_list[i]['viewercount']
    var metadata = api_data.metadata[api_data.metaindex[strim]]
    metadata.strim = strim
    metadata.viewercount = viewercount

    metadata.label = metadata.channel+" on "+metadata.platform
    if(metadata.hasOwnProperty('name') && metadata.name.length > 0){
      metadata.label = metadata.name+"\'s Channel"
    }
    metadata.live_class = metadata['live'] ? "label-success" : "label-danger"
    
    $('#strims > .row').last().append(card_template(metadata))
  }

  status = "<div class='label label-success' role='alert'>Tracking server is currently online with "+strim_list.length+" streams</div>"
  $('#status').html(status)
});
socket.on('error', function(error){
  console.log(error)
  status = "<div class='label label-warning col-md-4' role='alert'>Tracking server is currently offline.</div>"
  $('#status').html(status)
})

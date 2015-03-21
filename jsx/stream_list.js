// client side code
var socket = io('http://api.overrustle.com/streams');

// socket.on('connect', function(){
//   console.log("Connected SOCKET")
//   // we cannot infer this from the referrer because
//   // there is no way to set a referrer with this client
//   socket.emit("idle", {"/strims"})
//})

socket.on('error', function(error){
  console.log(error)
  status = "<div class='label label-warning col-md-4' role='alert'>Tracking server is currently offline.</div>"
  $('#status').html(status)
})

socket.on('strims', function(api_data){
  var viewercount = api_data["viewercount"]
  var strims = api_data["streams"]

  var strim_list = []
  
  $.each(strims, function(strim, svc){
    strim_list.push(api_data.metadata[api_data.metaindex[strim]])
  });
  strim_list.sort(function(a,b) {
    // give LIVE streams more weight in sorting higher
    var amulti = a.hasOwnProperty('metadata') && a['metadata'].hasOwnProperty('live') ? 10 : 1 ;
    var bmulti = b.hasOwnProperty('metadata') && b['metadata'].hasOwnProperty('live') ? 10 : 1 ;
    if (amulti*a.viewercount < bmulti*b.viewercount)
       return 1;
    if (amulti*a.viewercount > bmulti*b.viewercount)
      return -1;
    return 0;
  })

  $('#viewercount').html(viewercount)
  // drawMustache(strims, strim_list)
  $(document).trigger('strim_list', [strim_list])
  console.log('sending api data', api_data);
  $(document).trigger('api_data', [api_data])
});
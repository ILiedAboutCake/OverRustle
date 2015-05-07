function formatNumber (num) {
	return num.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1,")
}


$('#show-nsfw').prop('checked', localStorage.getItem("shownsfw")=="true")
$('#show-nsfw').change(function(){
  var shownsfw = $(this).prop('checked')
  console.log(shownsfw)
  localStorage.setItem("shownsfw", shownsfw);
  // TODO: refresh streams here
  if(typeof socket !== 'undefined'){
    socket.emit('api')    
  }
})

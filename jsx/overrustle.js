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

// chat resizing
var overlay = $('<div class="overlay"></div>');
var streampanel = $('.stream-box');
var chatpanel = $('.chat-box');
var resizebar = $('#chat-panel-resize-bar');
var minwidth = 320;

function resizeFrames(nwidth) {
  nwidth = Math.max(minwidth, nwidth);

  streampanel.css('width', 'calc(100% - ' + nwidth + 'px)');
  chatpanel.css('width', nwidth);
  resizebar.css('left', 0);

  localStorage.setItem('chatwidth', nwidth);
}

resizebar.on('mousedown.chatresize', function(e) {
  e.preventDefault();

  resizebar.addClass('active');

  overlay.appendTo('body');

  var offsetX = e.clientX;
  var sx = resizebar.position().left;

  $(document).on('mouseup.chatresize', function(e) {
    e.preventDefault();

    resizebar.removeClass('active');

    overlay.remove();

    resizebar.css('left', sx + e.clientX - offsetX);

    // unbind events that are not needed anymore
    $(document).unbind('mousemove.chatresize');
    $(document).unbind('mouseup.chatresize');

    resizeFrames(chatpanel.offset().left
      + chatpanel.outerWidth()
      - resizebar.offset().left);
  });

  $(document).on('mousemove.chatresize', function(e) {
    e.preventDefault();

    resizebar.css('left', sx + e.clientX - offsetX);
  });
});

// recall user's previous chat width
var width = parseInt(localStorage.getItem('chatwidth'), 10);
if (width > minwidth) {
  resizeFrames(width);
}

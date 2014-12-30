<br />
    <script type="text/javascript" src="//api.OverRustle.com/socket.io/socket.io.js"></script>
    <script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/mustache.js/0.8.1/mustache.min.js"></script>
<div class="container" id="strims_content">
  <div style="display: table; margin: 0 auto;">
    <div id="status"></div>
  </div>

  <h1 align="center" style="color: #FFFFFF;">See what <span id="viewercount"></span> rustlers are watching!</h1>
  <div id="strims">
    <div class="row"></div>
  </div>
  <br />
  <div style="text-align: center; color: #FFFFFF;">
    JSON API: <a href="http://api.overrustle.com/api">http://api.overrustle.com/api</a><br />
      <a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=6TUMKXJ23YGQG">Paypal</a> - 
      <a href="bitcoin:14j82o37d8AShtVP5LydYCptVxwMhbu4Z1">Bitcoin</a> - 
      <a href="dogecoin:DS6JdMnt96CVXEXZ2LNdjKq6kmcSD7mC88">Dogecoin</a> - 
      <a href="https://www.linode.com/?r=57232eb9908d0f24a8907e61106c88f475248ac7">Linode.com</a><Br />
    <a href="mailto:iliedaboutthecake@gmail.com">Contact</a> ILiedAboutCake - <?php echo date("Y"); ?><Br />
    <a href="https://github.com/ILiedAboutCake/OverRustle">Site</a> - <a href="https://github.com/ILiedAboutCake/OverRustle-API">API</a>  
</div><!-- /.container -->


<script type="text/javascript">
  // a hack to make this page look nice on "/destinychat"
  // TODO: refactor
  if(document.location.pathname === "/destinychat"){
    $('#strims_content').addClass("fill")
  }

  // client side code
  var socket = io('http://api.overrustle.com');

  socket.on('strims', function(api_data){
    var viewercount = api_data["viewercount"]
    $('#viewercount').html(viewercount)
    var strims = api_data["streams"]
    $('#strims').html('')
    new_html = ""
    var source   = $("#card").html();
    Mustache.parse(source)
    var card_template = function (data) {
      return Mustache.render(source, data);
    }
    $("#strims").html("<div class='row'></div>")
    var strim_list = []
    
    $.each(strims, function(strim, viewercount){
      strim_list.push({
        strim: strim,
        viewercount: viewercount
      })
    });
    strim_list.sort(function(a,b) {
      // give LIVE streams more weight in sorting higher
      var amulti = api_data.metadata[api_data.metaindex[a.strim]].live ? 10 : 1;
      var bmulti = api_data.metadata[api_data.metaindex[b.strim]].live ? 10 : 1;
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
      metadata.live_class = metadata.live ? "label-success" : "label-danger"
      
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
</script>

<script id="card" type="x-tmpl-mustache">
  <div class="col-xs-12 col-sm-6 col-md-4 col-lg-2">
    <div class="thumbnail">
      {{#live}}
      <a href="{{strim}}">
        <img class="stream-thumbnail" src="{{image_url}}" alt="{{channel}} on {{platform}} ">
      </a>
      {{/live}}
      <div class="caption">
        <div>
          <a href="{{strim}}">{{channel}} on {{platform}}</a>
          <span class="pull-right label label-as-badge {{live_class}}">
            {{viewercount}} <span class="glyphicon glyphicon-user" aria-hidden="true"></span> 
          </span>
        </div>
      </div>
    </div>
  </div>  
</script>

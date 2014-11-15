<br />
<script type="text/javascript" src="//OverRustle.com:9998/socket.io/socket.io.js"></script>

<div class="container">
<div class="row">
  <div class="col-md-4"></div>
  <script type="text/javascript">
  // client side code
  var socket = io();

  socket = io.connect('http://overrustle.com:9998');
  socket.on('strims', function(api_data){
    var viewercount = api_data["viewercount"]
    $('#viewercount').html(viewercount)
    var strims = api_data["streams"]
    $('#strims').html('')
    new_html = ""
    $.each(strims, function(strim, viewers){
      new_html = new_html + "<tr><td><a target='_blank' href='"+ strim +"'>"+ strim +"</a></td><td>~"+viewers+"</td></tr>"
    });
    $('#strims').html(new_html)
    status = "<div class='label label-success col-md-4' role='alert'>Tracking server is currently online.</div>"
    $('#status').html(status)
  });
  socket.on('error', function(error){
    console.log(error)
    status = "<div class='label label-warning col-md-4' role='alert'>Tracking server is currently offline.</div>"
    $('#status').html(status)
  })
  </script>
  
<div id="status">
</div>

  <div class="col-md-4"></div>
</div>

<h1 align="center" style="color: #FFFFFF;">See what <span id="viewercount"></span> rustlers are watching!</h1>
<table class="table" style="color: #FFFFFF;">
  <thead>
    <tr>
      <th>Stream Link</th>
      <th>Viewer Count</th>
    </tr>
  </thead>
  <tbody id="strims"></tbody>
</table>
<br />
<div style="text-align: center; color: #FFFFFF;">
  JSON API can be found at: <a href="http://overrustle.com:9998/api">http://overrustle.com:9998/api</a><br />
  Donate: 
    <a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=6TUMKXJ23YGQG">Paypal</a>, 
    <a href="bitcoin:14j82o37d8AShtVP5LydYCptVxwMhbu4Z1">Bitcoin</a>, 
    <a href="dogecoin:DS6JdMnt96CVXEXZ2LNdjKq6kmcSD7mC88">Dogecoin</a>, 
    <a href="https://www.linode.com/?r=57232eb9908d0f24a8907e61106c88f475248ac7">Linode.com Referral</a><Br />
  <a href="https://github.com/ILiedAboutCake/OverRustle">Github</a> - ILiedAboutCake 2014
</div>
    </div><!-- /.container -->
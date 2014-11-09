<br />
<div class="container">
<div class="row">
  <div class="col-md-4"></div>
<?php
//  Initiate curl
$ch = curl_init();
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_PORT, 9998);
curl_setopt($ch, CURLOPT_URL,"http://localhost:9998/api");
$result = curl_exec($ch);
$dankmemes = curl_getinfo($ch);
$data = json_decode($result, true);
arsort($data['streams']);


if(curl_exec($ch) === false)
{
  echo '<div class="label label-warning col-md-4" role="alert">Tracking server is currently offline.</div>';
}
else
{
  echo '<div class="label label-success col-md-4" role="alert">Tracking server is currently online. (' . round($dankmemes['total_time'], 3) . ' sec)</div>';
}
?>
  <div class="col-md-4"></div>
</div>

<h1 align="center" style="color: #FFFFFF;">See what <?php echo $data['totalviewers']; ?> rustlers are watching!</h1>
<table class="table" style="color: #FFFFFF;">
  <thead>
    <tr>
      <th>Stream Link</th>
      <th>Viewer Count</th>
    </tr>
  </thead>
<?php
foreach($data['streams'] as $name=>$viewers)
{
    $len = strlen($name);
    if($len > 128)
    {
      echo '<tr><td><a target="_blank" href=' . strip_tags($name) . '>Name Truncated (Spam/Advanced Stream)</a></td><td>' . strip_tags($viewers) . '</td></tr>';
    }
    else
    {
      echo '<tr><td><a target="_blank" href=' . strip_tags($name) . '>' . strip_tags($name) . '</a></td><td>~' . strip_tags($viewers) . '</td></tr>';
    }
}
curl_close($ch);
?>
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

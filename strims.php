<META HTTP-EQUIV="refresh" CONTENT="60">
<style>
    body {
        background-color: #121212;
        color: #999;
    }
    a:link {
        color: #999;
        text-decoration: none;
    }
    
    a:visited {
        color: #999;
        text-decoration: none;
    }

</style>
<?

//  Initiate curl
$ch = curl_init();
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_PORT, 9998);
curl_setopt($ch, CURLOPT_URL,"http://localhost:9998/api");
$result=curl_exec($ch);
$data = json_decode($result, true);

echo '<h1 align="center">Total Rustlers Connected: ~ ' . $data['totalviewers'] . '</h1>';
if(curl_exec($ch) === false)
{
    echo '<h2 align="center">Websocket Server is currently offline: ' . curl_error($ch);
    echo '<img src="https://camo.githubusercontent.com/fd6f52686fe2d0afd6cdf7df0b4de259aa46c143/68747470733a2f2f7261772e6769746875622e636f6d2f64657374696e7967672f776562736974652f6d61737465722f736372697074732f656d6f7465732f656d6f7469636f6e732f4269626c655468756d702e706e67">';
    echo '</h2>';
}
else
{
    echo '<h2 align="center">Websocket Server is currently Online';
    echo '<img src="https://camo.githubusercontent.com/e7e707d99e7d95ad2dd1ecebf326445c820dca77/68747470733a2f2f7261772e6769746875622e636f6d2f64657374696e7967672f776562736974652f6d61737465722f736372697074732f656d6f7465732f656d6f7469636f6e732f416e67656c5468756d702e706e67">';
    echo '</h2>';

}
echo "<h3>This script and backend are in early alpha. Expect restarts/incorrect viewer counts!</h3>";
echo "<table>";
echo "<tr><th>Stream</th><th>Viewer Count (estimate)</th></tr>";
foreach($data['streams'] as $name=>$viewers)
{
    $len = strlen($name);
    if($len > 128)
    {
    	echo '<tr><td><a href=' . $name . '>Name Truncated (Spam/Advanced Stream)</a></td><td>' . $viewers . '</td></tr>';
    }
    else
    {
    	echo '<tr><td><a href=' . $name . '>' . $name . '</a></td><td>~' . $viewers . '</td></tr>';
    }
}
echo "</table>";
echo "<br />Raw JSON API can be found at: http://overrustle.com:9998/api";
curl_close($ch);
?>
<h3>Love the project? Donations Accepted.</h3>
<b>Paypal</b>
<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
<input type="hidden" name="cmd" value="_s-xclick">
<input type="hidden" name="hosted_button_id" value="C5NKN3GW8FY8C">
<input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
<img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
</form>
<b>Bitcoin</b> <img src="https://camo.githubusercontent.com/6bfe76be07a30e870d389b2bf74f95cbe8202009/68747470733a2f2f7261772e6769746875622e636f6d2f64657374696e7967672f776562736974652f6d61737465722f736372697074732f656d6f7465732f656d6f7469636f6e732f574f5254482e706e67">
14j82o37d8AShtVP5LydYCptVxwMhbu4Z1<br />
<b>Dogecoin</b> <img src="https://camo.githubusercontent.com/b397b60422762ebe6c767b9f70208961420ec2aa/68747470733a2f2f7261772e6769746875622e636f6d2f64657374696e7967672f776562736974652f6d61737465722f736372697074732f656d6f7465732f656d6f7469636f6e732f536f446f67652e706e67">
DS6JdMnt96CVXEXZ2LNdjKq6kmcSD7mC88<br />
<b>Linode Referral: Help pay the server bill </b> <img src="https://camo.githubusercontent.com/61dad44ac8cbe776a31141ecd0280781e076ed6c/68747470733a2f2f7261772e6769746875622e636f6d2f64657374696e7967672f776562736974652f6d61737465722f736372697074732f656d6f7465732f656d6f7469636f6e732f4e6f54656172732e706e67">
https://www.linode.com/?r=57232eb9908d0f24a8907e61106c88f475248ac7<Br />

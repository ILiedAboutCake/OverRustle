<?php

//keyword ban list
$banlist = array(
	'phrase' => 'Ban Reason',
	'channelNameYouDoNotLike' => 'Hate speech why you do not like this person',
	);

//keeps people from embedding the site within itself
if(strpos($stream,'destiny.gg') !== false || (strpos($stream,'overrustle.com') !== false))
{
	header('Location: http://overrustle.com/destinychat');
}

//fixes a bug
if($stream == "#")
{
	header('Location: http://overrustle.com/destinychat');
}

//sends people back to destiny.gg for destiny's stream
if(strtolower($stream) == "destiny")
{
	header('Location: http://destiny.gg/bigscreen');
}

//keyword banning logic
foreach($banlist as $banItem => $banReason)
{
	if(stripos($stream, $banItem) !== false)
	{
		header("HTTP/1.0 500 Internal Server Error");
		echo "Phrase '" . $banItem . "' has been banned. Reason: " . $banReason;
		exit;
	}
}
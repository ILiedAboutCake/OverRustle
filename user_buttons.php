<li>
<div class="btn-group">

<?php

require_once 'config.php';

if (isset($_SESSION['user'])) { ?>
    <a href="/profile">
        <button class="btn btn-default navbar-btn" title="Profile">
            <span class="glyphicon glyphicon-user"></span>
        </button>
    </a>
    <a href="/logout">
        <button class="btn btn-default navbar-btn" title="Log Out">
            <span class="glyphicon glyphicon-off"></span>
        </button>
    </a>
<?php
} else {
    $twitch_url = 'https://api.twitch.tv/kraken/oauth2/authorize?response_type=code'
        . '&client_id='.$config['twitch_client_id']
        . '&redirect_uri='.$config['twitch_redirect_uri']
        . '&scope=user_read';
?>
    <a href="<?php echo $twitch_url ?>">
        <button class="btn btn-default navbar-btn" title="Log In with Twitch">
            <span>Log In</span>
        </button>
    </a>
<?php
}

?>

</div>
</li>

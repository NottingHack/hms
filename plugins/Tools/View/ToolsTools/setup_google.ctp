<?php

if ($authorised) {
?>

<p>Google is already setup for <?php echo($identity); ?></p>

<p><strong>Access Token:</strong> <?php echo($access_token); ?> (expires in <?php echo($expires_in); ?> seconds)<br />
<strong>Refresh Token:</strong> <?php echo($refresh_token); ?></p>


<?php
}
else {
?>

<p><strong>Note:</strong> You must only click the link if you are logged into the <?php echo($identity); ?> Google account.  If you click this when logged into your personal account, HMS will have access to all your calendars.</p>

<a href="<?php echo($authurl); ?>">Authorise Google</a>

<?php
}

?>
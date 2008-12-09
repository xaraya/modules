<?php

/**
 * Destroy the Vanilla session.
 * Ideally copy this file to the Vanilla base directory.
 */

// There should be one POST parameter, it being the session name and ID.
if (empty($_POST) || count($_POST) != 1) {
    exit('Invalid POST data');
}

// Only accept requests from the local host.
if ($_SERVER['REMOTE_ADDR'] != $_SERVER['SERVER_ADDR'] && $_SERVER['REMOTE_ADDR'] != 'localhost') {
    exit('Invalid address');
}

// The name of the sole POST parameter is the session name.
$session_name = array_shift(array_keys($_POST));

if (!is_string($session_name)) {
    exit('Invalid session name');
}

// CHECKME: if we don't set the cookie path and domain, then is the server going
// to have a problem knowing which session to lock onto? Or since we are destroying
// the session, perhaps it does not matter? We are not changing or deleting the
// session cookie, just deleting the session information on the server.
//session_set_cookie_params(0, $settings['COOKIE_PATH'], $settings['COOKIE_DOMAIN']);

// Start the session then destroy it.

// Set the session name.
session_name($session_name);

// Force the session details into the cookies array.
// This gets around the issue where the php flag session.use_only_cookies is
// set (so preventing the session starting on the POST alone).
$_COOKIE[$session_name] = $_POST[$session_name];

session_name($session_name);
session_start();
session_destroy();

exit(true);

?>
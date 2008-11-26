<?php

/**
 * Destroy the Vanilla session.
 * Ideally copy this file to the Vanilla base directory.
 */

// There should be one POST parameter, it being the session name and ID.
if (empty($_POST) || count($_POST) != 1) {
    exit('Invalid POST data');
}

$session_name = array_shift(array_keys($_POST));

if (!is_string($session_name)) {
    exit('Invalid session name');
}

// CHECKME: if we don't set the cookie path and domain, then is the server going
// to have a problem knowing which session to lock onto? Or since we are destroying
// the session, perhaps it does not matter? We are not changing or deleting the
// session cookie, just deleting the session information on the server.
//session_set_cookie_params(0, $settings['COOKIE_PATH'], $settings['COOKIE_DOMAIN']);

session_name($session_name);
session_start();
session_destroy();

exit(true);

?>
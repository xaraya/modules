<?php
if (xarUserIsLoggedIn()) {
    sys::import('modules.crispbb.class.tracker');
    $tracker = unserialize(xarModUserVars::get('crispbb', 'tracker_object'));
}
?>
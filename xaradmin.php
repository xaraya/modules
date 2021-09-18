<?php

if (xarUser::isLoggedIn()) {
    sys::import('modules.crispbb.class.tracker');
    $tracker = unserialize(xarModUserVars::get('crispbb', 'tracker_object'));
}

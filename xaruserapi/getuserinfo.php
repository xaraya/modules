<?php

/**
 * Returns information on a specific user.
 * @param uid integer User ID
 */

function xarbb_userapi_getuserinfo($args)
{
    static $users = array();
    extract $args;

    // uid is mandatory
    if (!isset($uid) || !is_numeric($uid) || $uid < 0) return;

    // If the user is cached, then return it.
    // Returning all details for now.
    if (isset($users[$uid])) return $users[$uid];

    // Get the details.
    $name = xarUserGetVar('name', $uid);

    // The url is set only if we have read permission.
    // TODO: these URLs, fetched from this central place, could be directed to some other place.
    if (xarSecurityCheck('ReadRole', 0, 'Roles', $uid)) {
        $display = xarModUrl('roles', 'user', 'display', array('uid' => $uid));
        $mailto = xarModUrl('roles', 'user', 'email', array('uid' => $item['xar_uid']));
    } else {
        $display = NULL;
        $mailto = NULL;
    }

    // TODO: detect and handle anonymous users appropriately.
}

?>
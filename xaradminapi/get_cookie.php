<?php

/**
 * Get a 'cookie' value from the user area, falling back
 * to a session variable if user is not logged on.
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2006 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.org
 *
 * @subpackage  xarbb Module
 * @author Jason Judge
 * @param name string Name of the value to store
 * @param value string Value to store
 * @todo Build this all into xarModGetUserVar()
*/

function xarbb_adminapi_get_cookie($args)
{
    extract($args);

    // We need a name/value pair.
    if (!isset($name) || !is_string($name)) return;

    if (xarUserIsLoggedIn()) {
        // Get from the user variable space
        $value = xarModGetUserVar('xarbb', $name);
    } else {
        // Store it in the session
        $value = xarSessionGetVar('xarbb_' . $name);
    }

    return $value;
}

?>
<?php

/**
 * Set a 'cookie' value in the user area, falling back
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
 * @todo Build this all into xarModSetUserVar()
*/

function xarbb_adminapi_set_cookie($args)
{
    extract($args);

    // We need a name/value pair.
    if (!isset($name) || !is_string($name) || !isset($value) || (!is_string($value) && !is_numeric($value))) return;

    if (xarUserIsLoggedIn()) {
        // Store in the user variable space
        // Module vars are created as needed, to ensure the ModUserVar is settable:
        //  -   f_{$forum_id)
        //  -   t_{$topic_id}  (Really? Seems like overkill)
        //  -   lastvisit
        //  -   all
        //xarModSetVar('xarbb', $name, '');
        xarModSetUserVar('xarbb', $name, $value);
    } else {
        // Store it in the session
        xarSessionSetVar('xarbb_' . $name, $value);
    }

    return;
}

?>
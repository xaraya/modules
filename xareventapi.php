<?php 

/**
 * Vanilla Forums
 *
 * @package modules
 * @copyright (C) 2002-2008 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Vanilla Module
 * @link http://xaraya.com/index.php/release/986.html
 * @author Jason Judge
 */

/**
 * Event handler for a user logging in.
 *
 * @return Boolean
 */

function vanilla_eventapi_OnUserLogin($arg)
{
    xarModAPIfunc('vanilla', 'user', 'loginevent');
    return true;
}

/**
 * Event handler for a user logging out.
 *
 * @return Boolean
 */

function vanilla_eventapi_OnUserLogout($arg)
{
    xarModAPIfunc('vanilla', 'user', 'logoutevent');
    return true;
}

?>
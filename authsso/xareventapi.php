<?php
/**
 * File: $Id$
 *
 * AuthSSO User API
 *
 * @package authentication
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.com
 *
 * @subpackage authsso
 * @author Jonn Beames <jsb@xaraya.com> | Richard Cave <rcave@xaraya.com>
*/

/**
 * Login the externally authenticated user on Server Request
 * 
 * @author Jonn Beames <jsb@xaraya.com>
 * @returns bool
 */

function authsso_eventapi_OnServerRequest()
{
    if (!xarUserIsLoggedIn() && (xarModGetName() != 'roles')) {
        xarModAPIFunc('roles', 'user', 'login', array('uname'=>'blah', 'pass'=>'blah'));
    }
    return;
}

?>

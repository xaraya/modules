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
 * check whether a user variable is avaiable from this module (currently unused)
 * @public
 * @author Marco Canini
 * @returns boolean
 */
function authsso_userapi_is_valid_variable($args)
{
// TODO: differentiate between read & update - might be different

    // ...some way to check if variable is valid...

    // Authsystem can handle all user variables
    return true;
}

?>

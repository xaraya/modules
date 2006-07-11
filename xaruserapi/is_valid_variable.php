<?php
/**
 * AuthphpBB2 
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.com
 *
 * @subpackage authldap
 * @link http://xaraya.com/index.php/release/77102.html
 * @author Alexander GQ Gerasiov <gq@gq.pp.ru>
*/

/**
 * check whether a user variable is avaiable from this module (currently unused)
 * @public
 * @returns boolean
 */
function authphpbb2_userapi_is_valid_variable($args)
{
// TODO: differentiate between read & update - might be different

    // ...some way to check if variable is valid...

    // Authsystem can handle all user variables
    return true;
}

?>
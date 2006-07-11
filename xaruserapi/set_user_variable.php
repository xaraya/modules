<?php
/**
 * AuthLDAP User API
 * 
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.com
 *
 * @subpackage authldap
 * @link http://xaraya.com/index.php/release/50.html
 * @author Chris Dudley <miko@xaraya.com>
 * @author Richard Cave <rcave@xaraya.com>
*/

/**
 * set a user variable (currently unused)
 * @public
 * @author Gregor J. Rothfuss
 * @param args['uid'] user id
 * @param args['name'] variable name
 * @param args['value'] variable value
 * @returns bool
 */
function authldap_userapi_set_user_variable($args)
{
    extract($args);

    if (!isset($uid) || !isset($name) || !isset($value)) {
        $msg = xarML('Empty uid (#(1)) or name (#(2)) or value (#(3)).', $uid, $name, $value);
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
    }

    // ...update the user variable in the external auth system if applicable...

    // throw back an exception if the user doesn't exist
    //if (...) {
    //    $msg = xarML('User identified by uid #(1) doesn\'t exist.', $uid);
    //    xarErrorSet(XAR_SYSTEM_EXCEPTION, 'ID_NOT_EXIST',
    //                  new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
    //    return;
    //}

    return true;
}

?>
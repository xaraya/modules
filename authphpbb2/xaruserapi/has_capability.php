<?php
/**
 * File: $Id$
 * 
 * AuthphpBB2 User API
 * 
 */

/**
 * check whether this module has a certain capability
 * @public
 * @param args['capability'] the capability to check for
 * @returns bool
 */
function authphpbb2_userapi_has_capability($args)
{
    extract($args);

    if (!isset($capability)) {
        $msg = xarML('Empty capability.');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
    }

    switch($capability) {
        case XARUSER_AUTH_FAILED:
            return true;
            break;
        case XARUSER_AUTH_DYNAMIC_USER_DATA_HANDLER:
        case XARUSER_AUTH_USER_ENUMERABLE:
        case XARUSER_AUTH_PERMISSIONS_OVERRIDER:
        case XARUSER_AUTH_USER_CREATEABLE:
        case XARUSER_AUTH_USER_DELETEABLE:
            return false;
            break;
    }
    $msg = xarML('Unknown capability.');
    xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                   new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
    return;
}

?>
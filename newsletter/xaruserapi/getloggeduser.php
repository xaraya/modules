<?php
/*
 * File: $Id: $
 *
 * Newsletter 
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003-2004 by the Xaraya Development Team
 * @link http://www.xaraya.com
 *
 * @subpackage newsletter module
 * @author Richard Cave <rcave@xaraya.com>
*/


/**
 * Retrieve a logged user's information from roles
 * 
 * @private
 * @author Richard Cave
 * @returns array
 * @return $userData
 */
function newsletter_userapi_getloggeduser()
{
    // Get logged userid
    $userId = xarSessionGetVar('uid'); 
    if ($userId == _XAR_ID_UNREGISTERED)
        return;

    // Get the user's data roles
    $userData = xarModAPIFunc('roles',
                              'user',
                              'get',
                              array('uid' => $userId));

    if (!isset($userData))
        return;
    else
        return $userData;
}

?>

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
 * The information block for Newsletter - same as main() user function
 *
 * @author Richard Cave
 * @returns array
 * @return $data
 */
function newsletter_user_information()
{
    // Security check
    //if(!xarSecurityCheck('OverviewNewsletter')) return;

    // Get the user menu
    $data = xarModAPIFunc('newsletter', 'user', 'menu');

    // Get information text
    $data['publishername'] = xarModGetVar('newsletter', 'publishername');
    $information = xarModGetVar('newsletter', 'information');
    $data['information'] = nl2br($information);

    // register info
    $data['registerlink'] = xarModURL('roles',
                                      'user',
                                      'register');

    // subscribe info
    $data['subscribelink'] = xarModURL('newsletter',
                                       'user',
                                       'newsubscription');

    // See if user is logged in
    if (xarUserIsLoggedIn()) {
        $data['loggedin'] = true;
    } else {
        $data['loggedin'] = false;
    }
        
    // Return the template variables defined in this function
    return $data;
}

?>

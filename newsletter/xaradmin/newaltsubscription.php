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
 * Add a single alternative subscription for a publication
 *
 * @public
 * @author Richard Cave
 * @returns array
 * @return $data
 */
function newsletter_admin_newaltsubscription()
{
    // Get the admin subscription menu
    $data['menu'] = xarModFunc('newsletter', 'admin', 'subscriptionmenu');

    // Options label
    $data['publishername'] = xarModGetVar('newsletter', 'publishername');
    $data['subscribebutton'] = xarVarPrepForDisplay(xarML('Add Subscription'));

    // Set startnum to display all publications
    $startnum = 1;

    // The user API function is called.
    $publications = xarModAPIFunc('newsletter',
                                  'user',
                                  'get',
                                  array('phase' => 'publication',
                                        'sortby' => 'title'));

    // Check for exceptions
    if (!isset($publications) && xarCurrentErrorType() != XAR_NO_EXCEPTION) 
        return; // throw back

    // Add the array of items to the template variables
    $data['publications'] = $publications;

    // Generate a one-time authorisation code for this operation
    $data['authid'] = xarSecGenAuthKey();

    // Return the template variables defined in this function
    return $data;
}


?>

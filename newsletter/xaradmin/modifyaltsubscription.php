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
 * Modify an Newsletter alternative subscription
 *
 * @public
 * @author Richard Cave
 * @param 'id' the id of the subscription
 * @returns array
 * @return $templateVarArray
 */
function newsletter_admin_modifyaltsubscription() 
{
    // Security check
    if(!xarSecurityCheck('EditNewsletter')) return;

    // Get parameters from the input
    if (!xarVarFetch('id', 'int:1:', $id)) return;

    // Get the list of publications
    $publications = xarModAPIFunc('newsletter',
                                  'user',
                                  'get',
                                  array('phase' => 'publication',
                                        'sortby' => 'title'));

    // Check for exceptions
    if (!isset($publications) && xarCurrentErrorType() != XAR_NO_EXCEPTION) 
        return; // throw back

    // Get the subscription information
    $subscription = xarModAPIFunc('newsletter',
                                  'user',
                                  'getaltsubscription',
                                  array('id' => $id));

    // Check for exceptions
    if (!isset($subscription) && xarCurrentErrorType() != XAR_NO_EXCEPTION) 
        return; // throw back

    // Add publications
    $subscription['publications'] = $publications;

    // See if the user is already subscribed
    for ($idx = 0; $idx < count($publications); $idx++) {
        
        // The user API function is called
        $subscriptions = xarModAPIFunc('newsletter',
                                       'user',
                                       'getaltsubscriptionbyemail',
                                        array('id' => 0, // doesn't matter
                                              'email' => $subscription['email'],
                                              'pid' => $publications[$idx]['id'],
                                              'phase' => 'altsubscription'));

        if (!$subscriptions) {
            $subscription['publications'][$idx]['checked'] = '';
        } else {
            $subscription['publications'][$idx]['checked'] = 'checked';
        }
    }

    // Set hook variables
    $item['module'] = 'newsletter';
    $hooks = xarModCallHooks('item','modify',$id,$item);
    if (empty($hooks) || !is_string($hooks)) {
        $hooks = '';
    }

    // Get the admin menu
    $menu = xarModAPIFunc('newsletter', 'admin', 'menu');

    // Return the template variables defined in this function
    $templateVarArray = array(
        'authid' => xarSecGenAuthKey(),
        'publishername' => xarModGetVar('newsletter', 'publishername'),
        'updatebutton' => xarVarPrepForDisplay(xarML('Update Subscription')),
        'menu' => $menu,
        'hooks' => $hooks,
        'subscription' => $subscription);

    // Return the template variables defined in this function
    return $templateVarArray;
}

?>

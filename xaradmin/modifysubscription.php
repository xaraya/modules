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
 * Modify an Newsletter subscription
 *
 * @public
 * @author Richard Cave
 * @returns array
 * @return $templateVarArray
 */
function newsletter_admin_modifysubscription() 
{
    // Security check
    if(!xarSecurityCheck('EditNewsletter')) return;

    // Get parameters from the input
    $data = array();
    if (!xarVarFetch('uid', 'int:1:', $uid)) return;

    // Get all the publications available
    $publications = xarModAPIFunc('newsletter',
                                  'user',
                                  'get',
                                  array('phase' => 'publication',
                                        'sortby' => 'title'));

    // Check for exceptions
    if (!isset($publications) && xarCurrentErrorType() != XAR_NO_EXCEPTION) 
        return; // throw back

    $data['publications'] = $publications;
    $data['htmlmail'] = false;
    $data['name'] = '';
    $data['uid'] = $uid;

    // See if the user is already subscribed
    for ($idx = 0; $idx < count($publications); $idx++) {
    
        // The user API function is called
        $subscriptions = xarModAPIFunc('newsletter',
                                       'user',
                                       'get',
                                        array('id' => 0, // doesn't matter
                                              'uid' => $uid,
                                              'pid' => $publications[$idx]['id'],
                                              'phase' => 'subscription'));

        if (!empty($subscriptions)) {
            $data['publications'][$idx]['checked'] = true;
            // Doesn't matter which subscription we grab - they
            // should all be either html or text mail
            $data['htmlmail'] = $subscriptions[0]['htmlmail'];
            $data['name'] = $subscriptions[0]['name'];
        } else {
            $data['publications'][$idx]['checked'] = false;
        }
    }

    // Set hook variables
    $item['module'] = 'newsletter';
    $hooks = xarModCallHooks('item','modify',$data['uid'],$item);
    if (empty($hooks) || !is_string($hooks)) {
        $hooks = '';
    }

    // Get the admin menu
    $menu = xarModAPIFunc('newsletter', 'admin', 'menu');

    // Return the template variables defined in this function
    $data['authid'] = xarSecGenAuthKey();
    $data['publishername'] = xarModGetVar('newsletter', 'publishername');
    $data['updatebutton'] = xarVarPrepForDisplay(xarML('Update Subscription'));
    $data['hooks'] = $hooks;
    $data['menu'] = $menu;

    // Return the template variables defined in this function
    return $data;
}

?>

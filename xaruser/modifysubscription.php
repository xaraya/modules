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
function newsletter_user_modifysubscription() 
{
    // Security check
    //if(!xarSecurityCheck('EditNewsletter')) return;

    // Get the user menu
    $data = xarModAPIFunc('newsletter', 'user', 'menu');

    // Verify that the user is logged in - the user has
    // to registered and in the roles table or else 
    // subscription is not possible
    if (!xarUserIsLoggedIn()) {
        $data['uid'] = 0;
        $data['loggedin'] = false;
        $hooks = '';
    } else {
        $data['loggedin'] = true;

        // No user id was passed, so get the user id 
        // of the current user assuming that the user is modifying 
        // their own subscription
        $data['uid'] = xarUserGetVar('uid');

        // Get publisher name

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

        // See if the user is already subscribed
        for ($idx = 0; $idx < count($publications); $idx++) {
        
            // The user API function is called
            $subscriptions = xarModAPIFunc('newsletter',
                                           'user',
                                           'get',
                                            array('id' => 0, // doesn't matter
                                                  'uid' => $data['uid'],
                                                  'pid' => $publications[$idx]['id'],
                                                  'phase' => 'subscription'));

            if (count($subscriptions) == 0) {
                $data['publications'][$idx]['checked'] = false;
            } else {
                $data['publications'][$idx]['checked'] = true;
                // Doesn't matter which subscription we grab - they
                // should all be either html or text mail
                $data['htmlmail'] = $subscriptions[0]['htmlmail'];
            }
        }

        // Set hook variables
        $item['module'] = 'newsletter';
        $hooks = xarModCallHooks('item','modify',$data['uid'],$item);
        if (empty($hooks) || !is_string($hooks)) {
            $hooks = '';
        }
    }

    // Return the template variables defined in this function
    $templateVarArray = array('authid' => xarSecGenAuthKey(),
        'publishername' => xarModGetVar('newsletter', 'publishername'),
        'updatebutton' => xarVarPrepForDisplay(xarML('Update Subscription')),
        'hooks' => $hooks,
        'data' => $data);

    // Return the template variables defined in this function
    return $templateVarArray;
}

?>

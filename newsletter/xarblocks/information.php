<?php 
/*
 * File: $Id: $
 *
 * Newsletter 
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003-2004 by the Xaraya Development Team
 * @link http://wwwk.schwabfoundation.org
 *
 * @subpackage newsletter module
 * @author Richard Cave <caveman : rcave@xaraya.com>
*/


/**
 * init func
 */
function newsletter_informationblock_init()
{
    //xarSecAddSchema(0, 'Newsletter:Newsletterblock', 'Block title');
    return true;
}

/**
 * info array
 */
function newsletter_informationblock_info()
{
    return array('text_type' => 'Information',
         'module' => 'newsletter',
         'text_type_long' => "Displays Newsletter Information",
         'allow_multiple' => false,
         'form_content' => false,
         'form_refresh' => false,
         'show_preview' => true);
}

/**
 * Display func
 */
function newsletter_informationblock_display($blockinfo)
{
    // Security check
    //if(!xarSecurityCheck('ViewLogin')) return;

    // Get variables from content block
    $vars = @unserialize($blockinfo['content']);

    // Create menulinks
    $data = array();

    // Get the current user
    if (!xarUserIsLoggedIn()) {
        $data['loggedin'] = false;

        $data['infolink'] = Array('url'   => xarModURL('newsletter',
                                                       'user',
                                                       'information'),
                                  'title' => xarML('Information about newsletter publications'),
                                  'label' => xarML('Information'));

    } else {
        $data['loggedin'] = true;

        // Get user id
        $uid = xarUserGetVar('uid');

        // See if this user has already subscribed
        $subscriptions = xarModAPIFunc('newsletter',
                                       'user',
                                       'getsubscription',
                                       array('id' => 0, // doesn't matter
                                             'uid' => $uid));

        if (count($subscriptions) == 0) {
            $data['subscribelink'] = Array('url'   => xarModURL('newsletter',
                                                                'user',
                                                                'newsubscription'),
                                           'title' => xarML('Subscribe to an newsletter'),
                                           'label' => xarML('Subscribe'));
        } else {
            $data['subscribelink'] = Array('url'   => xarModURL('newsletter',
                                                                'user',
                                                                'modifysubscription'),
                                           'title' => xarML('Modify your subscription to an newsletter'),
                                           'label' => xarML('Modify Subscription'));
        }
    }

    $data['viewlink'] = Array('url'   => xarModURL('newsletter',
                                                   'user',
                                                   'viewarchives'),
                              'title' => xarML('View archives'),
                              'label' => xarML('View Archives'));
 
    // Return content
    $blockinfo['content'] = $data;

    return $blockinfo;
}
?>

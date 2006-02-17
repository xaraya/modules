<?php
/**
 * View a list of items
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Sigmapersonnel Module
 * @author Michel V.
 */
/**
 * view a list of presence items of this user
 * This is a standard function to provide an overview of all of the items
 * available from the module.
 * @todo Do we need this?
 */
function sigmapersonnel_user_viewpresence($args)
{
    extract($args);
    if (!xarVarFetch('startnum', 'str:1:', $startnum, '1', XARVAR_NOT_REQUIRED)) return;
    // if (!xarVarFetch('uid', 'int:1:', $uid, xarUserGetVar('uid'), XARVAR_NOT_REQUIRED)) return;
    // uid should not be taken like this. Only when there is a higher level of permission

    if (!xarSecurityCheck('ViewSIGMAPresence')) return;

    $data = xarModAPIFunc('sigmapersonnel', 'user', 'menu');
    // Prepare the variable that will hold some status message if necessary
    $data['status'] = '';
    // Prepare the array variable that will hold all items for display
    $data['items'] = array();
    // Specify some other variables for use in the function template
    $data['header'] = xarML('Your presence');
    $data['pager'] = '';
    // Security check - important to do this as early as possible to avoid
    // potential security holes or just too much wasted processing
/*
    $uid = xarUserGetVar('uid');
    $items = xarModAPIFunc('sigmapersonnel',
                           'user',
                           'getallpresence',
                           array('startnum' => $startnum,
                                 'numitems' => xarModGetUserVar('sigmapersonnel','itemsperpage',$uid)));
    if (!isset($items) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; // throw back

    // TODO: check for conflicts between transformation hook output and xarVarPrepForDisplay
    // Loop through each item and display it.
    foreach ($items as $item) {

        // We want to get the first item here
        // That has the most recent date and presence type
        // Security check 2 - if the user has read access to the item, show a
        // link to display the details of the item
        if (xarSecurityCheck('ReadSIGMAPresence', 0, 'PresenceItem', "$item[pid]:$uid:All")) {// TODO: Improve this
            $item['displaylink'] = xarModURL('sigmapersonnel',
                                      'user',
                                      'displaypresence',
                                       array('pid' => $item['pid']));
            // Security check 2 - else only display the item name (or whatever is
            // appropriate for your module)
        } else {
            $item['displaylink'] = '';
        }
        // Clean up the item text before display
        $item['name'] = xarVarPrepForDisplay($item['name']);
        // Add this item to the list of items to be displayed
        $data['items'][] = $item;
    }
    // TODO: how to integrate cat ids in pager (automatically) when needed ???
    // Get the UID so we can see if there are any overridden defaults.
    $uid = xarUserGetVar('uid');
    // Call the xarTPL helper function to produce a pager in case of there
    // being many items to display.

    // Note that this function includes another user API function.  The
    // function returns a simple count of the total number of items in the item
    // table so that the pager function can do its job properly
    $data['pager'] = xarTplGetPager($startnum,
        xarModAPIFunc('sigmapersonnel', 'user', 'countitems'),
        xarModURL('sigmapersonnel', 'user', 'viewpresence', array('startnum' => '%%')),
        xarModGetUserVar('sigmapersonnel', 'itemsperpage', $uid));
    // Specify some other variables for use in the function template
    $data['someheader'] = xarML('Example item name');

    // Build currentpresence type
    $data['currentpresence'] = '';
    // Get latest item entered
    // Then determine the itemtypeid of that one = currentpresence
    // If not set, then presence is unknown (safest)

*/      $lists = xarModAPIfunc(
            'lists', 'user', 'getlistitems',
            array(
                'lid' => 5
            )
        );
        if(empty($list)) {
            echo "shit"; return;
        }
    $data['list'] = $lists;
    // Same as above.  We are changing the name of the page to raise
    // better search engine compatibility.
    xarTplSetPageTitle(xarVarPrepForDisplay(xarML('View Current presence')));
    // Return the template variables defined in this function
    return $data;

}
?>
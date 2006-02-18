<?php
/**
 * View a list of items
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Maxercalls Module
 * @link http://xaraya.com/index.php/release/247.html
 * @author Maxercalls module development team
 */
/**
 * view a list of calls of this user
 * This is a standard function to provide an overview of all of the items
 * available from the module.
 *
 * @return array
 */
function maxercalls_user_view($args)
{
    extract ($args);
    if (!xarVarFetch('startnum', 'int:1:', $startnum, '1', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('catid', 'int:1:', $catid, '', XARVAR_NOT_REQUIRED)) return;

    $data = xarModAPIFunc('maxercalls', 'user', 'menu');
    // Prepare the variable that will hold some status message if necessary
    $data['status'] = '';
    // Prepare the array variable that will hold all items for display
    $data['calldatelabel']  = xarVarPrepForDisplay(xarML('Date of call'));
    $data['calltimelabel']  = xarVarPrepForDisplay(xarML('Time of call'));
    $data['calltextlabel']  = xarVarPrepForDisplay(xarML('Text of call'));
    $data['entertslabel']   = xarVarPrepForDisplay(xarML('Entered on'));
    $data['enteredbylabel'] = xarVarPrepForDisplay(xarML('Entered by'));
    $data['remarkslabel']   = xarVarPrepForDisplay(xarML('Remarks'));
    $data['optionslabel']   = xarVarPrepForDisplay(xarML('Admin Options'));
    $data['pager']          = '';
    $data['catid']          = $catid;

    if (!xarSecurityCheck('ViewMaxercalls')) return;
    // Lets get the UID of the current user to check for overridden defaults
    $uid = xarUserGetVar('uid');
    // The API function is called.  The arguments to the function are passed in
    // as their own arguments array.
    // Security check 1 - the getall() function only returns items for which the
    // the user has at least OVERVIEW access.
    $data['items'] = array();
    $items = xarModAPIFunc('maxercalls',
        'user',
        'getall',
        array('startnum' => $startnum,
              'numitems' => xarModGetUserVar('maxercalls','itemsperpage',$uid),
              'catid'    => $catid,
              'uid'      => $uid));
    if (!isset($items) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; // throw back

    // TODO: check for conflicts between transformation hook output and xarVarPrepForDisplay
    // Loop through each item and display it.
    foreach ($items as $item) {
        // Security check 2 - if the user has read access to the item, show a
        // link to display the details of the item
        $owner = xarUserGetVar('uid');
        if (xarSecurityCheck('ReadMaxercalls', 0, 'Item', "All:All:$owner")) {
            $item['link'] = xarModURL('maxercalls','user','display',
                                       array('callid' => $item['callid']));
            // Security check 2 - else only display the item name (or whatever is
            // appropriate for your module)
        } else {
            $item['link'] = '';
        }
        $ddname = 'calltext';
        // Clean up the item text before display
        $item['calldate'] = xarVarPrepForDisplay($item['calldate']);
        $item['calltime'] = xarVarPrepForDisplay($item['calltime']);
        $item['calltext'] = xarModAPIFunc('dynamicdata','user','getfield',
                            array ('module' => 'maxercalls',
                                   'itemtype' =>3,
                                   'itemid' => $item['calltext'],
                                   'name' => 'calltext'));

        // Add this item to the list of items to be displayed
        $data['items'][] = $item;
    }
    // TODO: how to integrate cat ids in pager (automatically) when needed ???
    // Get the UID so we can see if there are any overridden defaults.
    $uid = xarUserGetVar('uid');

    $data['pager'] = xarTplGetPager($startnum,
        xarModAPIFunc('maxercalls', 'user', 'countitems'),
        xarModURL('maxercalls', 'user', 'view', array('startnum' => '%%', 'catid' => $catid)),
        xarModGetUserVar('maxercalls', 'itemsperpage', $uid));
    // Same as above.  We are changing the name of the page to raise
    // better search engine compatibility.
    xarTplSetPageTitle(xarVarPrepForDisplay(xarML('View your calls')));
    // Return the template variables defined in this function
    return $data;
}

?>

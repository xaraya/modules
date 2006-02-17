<?php
/**
 * Standard function to view items
 *
 * @package modules
 * @copyright (C) 2005-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Maxercalls Module
 * @link http://xaraya.com/index.php/release/247.html
 * @author Maxercalls module development team
 */
/**
 * view calls
 *
 * @return array
 */
function maxercalls_admin_viewcalls($args)
{
    extract ($args);
    // Get parameters from whatever input we need.
    if (!xarVarFetch('startnum', 'str:1:', $startnum, '1', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('catid','int:1:', $catid, '', XARVAR_NOT_REQUIRED)) return;

    if (!xarSecurityCheck('DeleteMaxercalls', 1)) return;

    $data = xarModAPIFunc('maxercalls', 'admin', 'menu');
    // Initialise the variable that will hold the items, so that the template
    // doesn't need to be adapted in case of errors
    $data['items'] = array();
    // Specify some labels for display
    $data['calldatelabel'] = xarVarPrepForDisplay(xarML('Date of call'));
    $data['calltimelabel'] = xarVarPrepForDisplay(xarML('Time of call'));
    $data['calltextlabel'] = xarVarPrepForDisplay(xarML('Text of the call'));
    $data['ownerlabel'] = xarVarPrepForDisplay(xarML('Owner of maxer'));
    $data['enteredbylabel'] = xarVarPrepForDisplay(xarML('Entered by'));
    $data['remarkslabel'] = xarVarPrepForDisplay(xarML('Remarks'));
    $data['entertslabel'] = xarVarPrepForDisplay(xarML('Date and time entered'));
    $data['optionslabel'] = xarVarPrepForDisplay(xarML('Admin Options'));
    $data['catid'] = $catid;
    // Call the xarTPL helper function to produce a pager in case of there
    // being many items to display.

    // Note that this function includes another user API function.  The
    // function returns a simple count of the total number of items in the item
    // table so that the pager function can do its job properly
    $data['pager'] = xarTplGetPager($startnum,
        xarModAPIFunc('maxercalls', 'admin', 'countitems'),
        xarModURL('maxercalls', 'admin', 'viewcalls', array('startnum' => '%%', 'catid' => $catid)),
        xarModGetVar('maxercalls', 'itemsperpage'));
    // Security check - important to do this as early as possible to avoid
    // potential security holes or just too much wasted processing
    if (!xarSecurityCheck('EditMaxercalls')) return;
    // The admin API function is called.  This takes the number of items
    // required and the first number in the list of all items, which we
    // obtained from the input and gets us the information on the appropriate
    // items.
    $items = xarModAPIFunc('maxercalls',
        'user',
        'getall',
        array('startnum' => $startnum,
              'numitems' => xarModGetVar('maxercalls', 'itemsperpage'),
              'catid'    => $catid));
    // Check for exceptions
    if (!isset($item) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; // throw back

    // Check individual permissions for Edit / Delete
    // Note : we could use a foreach ($items as $item) here as well, as
    // shown in xaruser.php, but as an maxercalls, we'll adapt the $items array
    // 'in place', and *then* pass the complete items array to $data
    for ($i = 0; $i < count($items); $i++) {
        $item = $items[$i];
        if (xarSecurityCheck('EditMaxercalls', 0, 'Item', "$item[callid]:All:$item[enteruid]")) {
            $items[$i]['editurl'] = xarModURL('maxercalls',
                'admin',
                'modifycall',
                array('callid' => $item['callid']));
        } else {
            $items[$i]['editurl'] = '';
        }
        $items[$i]['edittitle'] = xarML('Edit');
        if (xarSecurityCheck('DeleteMaxercalls', 0, 'Item', "$item[callid]:All:$item[enteruid]")) {
            $items[$i]['deleteurl'] = xarModURL('maxercalls',
                'admin',
                'deletecall',
                array('callid' => $item['callid']));
        } else {
            $items[$i]['deleteurl'] = '';
        }
        $items[$i]['deletetitle'] = xarML('Delete');
    }

        foreach ($items as $item) {
        // Security check 2 - if the user has read access to the item, show a
        // link to display the details of the item
        $owner = xarUserGetVar('uid');
        if (xarSecurityCheck('ReadMaxercalls', 0, 'Item', "All:All:$owner")) {
            $item['link'] = xarModURL('maxercalls',
                'user',
                'display',
                array('callid' => $item['callid']));
            // Security check 2 - else only display the item name (or whatever is
            // appropriate for your module)
        } else {
            $item['link'] = '';
        }
        // Clean up the item text before display
        $item['calldate'] = xarVarPrepForDisplay($item['calldate']);
        $item['calltime'] = xarVarPrepForDisplay($item['calltime']);
        $item['calltext'] = xarModAPIFunc('dynamicdata','user','getfield',
                            array ('module' => 'maxercalls',
                                   'itemtype' =>3,
                                   'itemid' => $item['calltext'],
                                   'name' => 'calltext'));
        $item['owner'] = xarUserGetVar('name', $item['owner']);

        // Add this item to the list of items to be displayed
        $data['items'][] = $item;
    }
    // Return the template variables defined in this function
    return $data;

}

?>

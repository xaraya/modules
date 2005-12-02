<?php
/**
* Display GUI for config modification
*
* @package unassigned
* @copyright (C) 2002-2005 by The Digital Development Foundation
* @license GPL {@link http://www.gnu.org/licenses/gpl.html}
* @link http://www.xaraya.com
*
* @subpackage ebulletin
* @link http://xaraya.com/index.php/release/557.html
* @author Curtis Farnham <curtis@farnham.com>
*/
/**
 * View publications
 */
function ebulletin_admin_viewsubscribers()
{
    // security check
    if (!xarSecurityCheck('EditeBulletin')) return;

    // get HTTP vars
    if (!xarVarFetch('startnum', 'str:1:', $startnum, '1', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('sort', 'str:1:', $sort, 'DESC', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('order', 'str:1:', $order, 'name', XARVAR_NOT_REQUIRED)) return;

    // get other vars
    $numitems = xarModGetVar('ebulletin', 'admin_subsperpage');

    // get subscribers
    $subscribers = xarModAPIFunc('ebulletin', 'user', 'getallsubscribers', array(
        'startnum' => $startnum, 'numitems' => $numitems)
    );
    if (empty($subscribers) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return;

    // add a string describing whether subscriber is registered on this site
    $regyes = xarML('Yes');
    $regno = xarML('No');
    foreach ($subscribers as $index => $subscriber) {
        $subscribers[$index]['reg_string'] = ($subscriber['registered']) ? $regyes : $regno;
    }

    // get pager
    $pager = xarTplGetPager($startnum,
        xarModAPIFunc('ebulletin', 'user', 'countsubscribers'),
        xarServerGetCurrentURL(array('startnum' => '%%')), $numitems);

    // get whether we should show an "add subscribers" link
    $showaddlink = xarSecurityCheck('AddeBulletin', 0);

/*
    // generate list of options for each publication
    foreach ($pubs as $index => $pub) {

        $pubs[$index]['editurl'] = '';
        $pubs[$index]['deleteurl'] = '';
        $pubs[$index]['issueurl'] = '';

        // Modify
        if (xarSecurityCheck('EditeBulletin', 0, 'Publication', "$pub[name]:$pub[id]")) {
            $pubs[$index]['editurl'] = xarModURL('ebulletin', 'admin', 'modify',
                                                 array('id' => $pub['id']));
        }

        // New Issue
        if (xarSecurityCheck('AddeBulletin', 0, 'Publication', "$pub[name]:All:$pub[id]")) {
            $pubs[$index]['issueurl'] = xarModURL('ebulletin', 'admin', 'newissue',
                                                  array('pid' => $pub['id']));
        }

        // Delete
        if (xarSecurityCheck('DeleteeBulletin', 0, 'Publication', "$pub[name]:All:$pub[id]")) {
            $pubs[$index]['deleteurl'] = xarModURL('ebulletin', 'admin', 'delete',
                                                   array('id' => $pub['id']));
        }

    }
*/

    // initialize template array
    $data = xarModAPIFunc('ebulletin', 'admin', 'menu');

    // add template vars
#    $data['pubs'] = $pubs;
    $data['subscribers'] = $subscribers;
    $data['pager'] = $pager;
    $data['showaddlink'] = $showaddlink;

    return $data;

}

?>

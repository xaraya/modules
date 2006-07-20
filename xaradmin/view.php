<?php
/**
 * Sniffer System
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Sniffer Module
 * @link http://xaraya.com/index.php/release/775.html
 * @author Frank Besler using phpSniffer by Roger Raymond
 */
/**
 * View a list of sniffer items
 *
 * @author Richard Cave
 * @param 'startnum' starting number to display
 * @param 'sortby' sort by agent, os, etc.
 * @returns array
 * @return $data
 */
function sniffer_admin_view()
{
    // Get parameters from the input
    if (!xarVarFetch('startnum', 'int:0:', $startnum, 1)) return;
    if (!xarVarFetch('sortby', 'str:1:', $sortby, 'id')) return;

    // Get the user menu
    $data = xarModAPIFunc('sniffer', 'admin', 'menu');

     // Prepare the array variable that will hold all items for display
    $data['startnum'] = $startnum;
    $data['sortby'] = $sortby;

    // The user API function is called.
    $items = xarModAPIFunc('sniffer',
                           'user',
                           'get',
                           array('startnum' => $startnum,
                                 'numitems' => xarModGetVar('sniffer',
                                                            'itemsperpage'),
                                 'sortby' => $sortby));

    // Check individual permissions for Delete
    if(xarSecurityCheck('DeleteSniffer', 0)) {
        $allowdelete = true;
    } else {
        $allowdelete = false;
    }

    for ($i = 0; $i < count($items); $i++) {
        $item = $items[$i];

        $items[$i]['deletetitle'] = xarML('Delete');
        if ($allowdelete) {
                $items[$i]['deleteurl'] = xarModURL('sniffer',
                                                    'admin',
                                                    'delete',
                                                    array('id' => $item['id'],
                                                          'startnum' => $startnum,
                                                          'sortby' => $sortby));
        } else {
            $items[$i]['deleteurl'] = '';
        }
    }
    $data['items'] = $items;

    // Create sort by URLs
    if ($sortby != 'agent' ) {
        $data['agentlink'] = xarModURL('sniffer',
                                       'admin',
                                       'view',
                                       array('startnum' => 1,
                                             'sortby' => 'agent'));
    } else {
        $data['agentlink'] = '';
    }
    if ($sortby != 'osnam' ) {
        $data['osnamlink'] = xarModURL('sniffer',
                                       'admin',
                                       'view',
                                       array('startnum' => 1,
                                             'sortby' => 'osnam'));
    } else {
        $data['osnamlink'] = '';
    }
    if ($sortby != 'osver' ) {
        $data['osverlink'] = xarModURL('sniffer',
                                       'admin',
                                       'view',
                                       array('startnum' => 1,
                                             'sortby' => 'osver'));
    } else {
        $data['osverlink'] = '';
    }
    if ($sortby != 'agnam' ) {
        $data['agnamlink'] = xarModURL('sniffer',
                                       'admin',
                                       'view',
                                       array('startnum' => 1,
                                             'sortby' => 'agnam'));
    } else {
        $data['agnamlink'] = '';
    }
    if ($sortby != 'agver' ) {
        $data['agverlink'] = xarModURL('sniffer',
                                       'admin',
                                       'view',
                                       array('startnum' => 1,
                                             'sortby' => 'agver'));
    } else {
        $data['agverlink'] = '';
    }

    $data['pager'] = xarTplGetPager($startnum,
                                    xarModAPIFunc('sniffer',
                                                  'user',
                                                  'countitems'),
                                    xarModURL('sniffer',
                                              'admin',
                                              'view',
                                              array('startnum' => '%%',
                                                   'sortby' => $sortby)),
                                    xarModGetVar('sniffer', 'itemsperpage'));


    // Return the template variables defined in this function
    return $data;
}

?>

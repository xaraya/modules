<?php
/**
 * Standard function to view items
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Sigmapersonnel Module
 * @link http://xaraya.com/index.php/release/418.html
 * @author MichelV.
 */
/**
 * Standard function to view items
 *
 * @author MichelV <michelv@xarayahosting.nl>
 * @param int startnum
 * @param id catid
 * @param str sortby
 * @return array
 */
function sigmapersonnel_admin_viewpersons()
{
    // Get parameters from whatever input we need.
    if (!xarVarFetch('startnum', 'str:1:', $startnum, '1', XARVAR_NOT_REQUIRED)) return;
    // Catid is the group someone belongs to
    if (!xarVarFetch('catid',    'id',     $catid,    NULL, XARVAR_DONT_SET)) return;
    if (!xarVarFetch('sortby',   'str:1:', $sortby,   'lastname')) return;

    // Initialise the $data variable that will hold the data
    $data = xarModAPIFunc('sigmapersonnel', 'admin', 'menu');
    // Initialise the variable that will hold the items, so that the template
    // doesn't need to be adapted in case of errors
    $data['items'] = array();

    // Call the xarTPL helper function to produce a pager
    $data['pager'] = xarTplGetPager($startnum,
        xarModAPIFunc('sigmapersonnel', 'user', 'countitems'),
        xarModURL('sigmapersonnel', 'admin', 'view', array('startnum' => '%%','sortby' => $sortby, 'catid' => $catid)),
        xarModGetVar('sigmapersonnel', 'itemsperpage'));
    // Security check
    if (!xarSecurityCheck('EditSIGMAPersonnel')) return;
    // The user API function is called.
    $items = xarModAPIFunc('sigmapersonnel',
                           'user',
                           'getall',
                            array('startnum' => $startnum,
                                  'numitems' => xarModGetVar('sigmapersonnel','itemsperpage'),
                                  'sortby'   => $sortby,
                                  'catid'    => $catid));
    // Check for exceptions
    if (!isset($items) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; // throw back

    // Create sort by URLs
    if ($sortby != 'lastname' ) {
        $data['slastnamelink'] = xarModURL('sigmapersonnel',
                                       'admin',
                                       'viewpersons',
                                       array('startnum' => 1,
                                             'sortby' => 'lastname',
                                             'catid' => $catid));
    } else {
        $data['slastnamelink'] = '';
    }
    if ($sortby != 'firstname' ) {
        $data['sfirstnamelink'] = xarModURL('sigmapersonnel',
                                       'admin',
                                       'viewpersons',
                                       array('startnum' => 1,
                                             'sortby' => 'firstname',
                                             'catid' => $catid));
    } else {
        $data['sfirtnamelink'] = '';
    }
    if ($sortby != 'pnumber' ) {
        $data['spnumberlink'] = xarModURL('sigmapersonnel',
                                       'admin',
                                       'viewpersons',
                                       array('startnum' => 1,
                                             'sortby' => 'pnumber',
                                             'catid' => $catid));
    } else {
        $data['spnumberlink'] = '';
    }



    // Check individual permissions for Edit / Delete
    for ($i = 0; $i < count($items); $i++) {
        $item = $items[$i];
        if (xarSecurityCheck('EditSIGMAPersonnel', 0, 'PersonnelItem', "$item[personid]:All:$item[persstatus]")) { //$catid goes in here as well
            $items[$i]['editurl'] = xarModURL('sigmapersonnel',
                'admin',
                'modifyperson',
                array('personid' => $item['personid']));
        } else {
            $items[$i]['editurl'] = '';
        }
        $items[$i]['edittitle'] = xarML('Edit');
        if (xarSecurityCheck('DeleteSIGMAPersonnel', 0, 'PersonnelItem', "$item[personid]:All:$item[persstatus]")) {
            $items[$i]['deleteurl'] = xarModURL('sigmapersonnel',
                'admin',
                'deleteperson',
                array('personid' => $item['personid']));
        } else {
            $items[$i]['deleteurl'] = '';
        }
        $items[$i]['deletetitle'] = xarML('Delete');
    }
    // Add the array of items to the template variables
    $data['items'] = $items;
    $data['catid'] = $catid;
    // Return the template variables defined in this function
    return $data;
}
?>
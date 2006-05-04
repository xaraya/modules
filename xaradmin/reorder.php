<?php
/**
 * Reorder forum items - a simple approach
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2005 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage xarbb
 * @author jojodee
 */
/**
 * reorder items
 */
function xarbb_admin_reorder()
{
    if (!xarVarFetch('startnum', 'str:1:', $startnum, '1', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('moveaction', 'str:1:', $moveaction, NULL, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('cfid', 'int:0:', $cfid, NULL, XARVAR_NOT_REQUIRED)) return;

    // Initialise the variable that will hold the items, so that the template
    // doesn't need to be adapted in case of errors
    $data['items'] = array();

    if (!isset($itemsperpage)) {
        $itemsperpage=20;
    }

    $data['pager'] = xarTplGetPager($startnum,
        xarModAPIFunc('xarbb', 'user', 'countforums'),
        xarModURL('xarbb', 'admin', 'reorder', array('startnum' => '%%')),
        xarModGetVar('xarbb', 'itemsperpage')
    );
 
    if (!xarSecurityCheck('EditxarBB')) return;

    $items = xarModAPIFunc('xarbb', 'user', 'getallforums',
        array('startnum' => $startnum, 'numitems' => xarModGetVar('xarbb', 'itemsperpage'))
    );

    // Check for exceptions
    if (!isset($items) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; // throw back
    

    $totalitems=count($items);
    for ($i = 0; $i < $totalitems; $i++) {
        $item = $items[$i];
        if (xarSecurityCheck('EditxarBB', 0) && ($i <>0)) {
            $items[$i]['upurl'] = xarModURL('xarbb', 'admin', 'reorder',
                array('cfid' => $item['fid'],'moveaction' => 'up')
            );
       }elseif (xarSecurityCheck('EditxarBB', 0) && ($i == 0)) {
            $items[$i]['upurl'] = '';
            $items[$i]['uptitle'] = '';
        } else {
            $items[$i]['upurl'] = '';
        }
        $items[$i]['uptitle'] = xarML('Move Up');
        if (xarSecurityCheck('EditxarBB', 0) && ($i <>$totalitems-1)) {
            $items[$i]['downurl'] = xarModURL('xarbb', 'admin', 'reorder',
                array('cfid' => $item['fid'], 'moveaction' => 'down')
            );
            $items[$i]['downtitle'] = xarML('Move Down');
        } elseif (xarSecurityCheck('EditxarBB', 0) && ($i == $totalitems-1)) {
            $items[$i]['downurl'] = '';
            $items[$i]['downtitle'] = '';
        } else {
            $items[$i]['downurl'] = '';
            $items[$i]['downtitle'] = xarML('Move Down');
        }
        $items[$i]['calcorder'] = $i+1; 
        //this is the actual position - we *can not* assume contiguous numbering 
        // Forums are created wtih an order id equal to the fid and forums get deleted ..
   }

   if (!empty($cfid) && !empty($moveaction)) {
       $domove=xarModAPIFunc('xarbb', 'admin', 'moveforum', array('cfid' => $cfid, 'moveaction' => $moveaction));

       if (!$domove) {
            return;
       } else{
            xarResponseRedirect(xarModURL('xarbb', 'admin', 'reorder'));
       }
    }

    // Add the array of items to the template variables
    $data['items'] = $items; 
 
    // Return the template variables defined in this function
    return $data;

} 

?>
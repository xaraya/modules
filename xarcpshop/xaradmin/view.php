<?php
/**
 * File: $Id:
 * @copyright (C) 2004 by Jo Dalle Nogare
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://xaraya.athomeandabout.com
 *
 * @subpackage xarcpshop
 * @author jojodee@xaraya.com
 */
/**
 * view items
 */
function xarcpshop_admin_view()
{ 
    if (!xarVarFetch('startnum', 'str:1:', $startnum, '1', XARVAR_NOT_REQUIRED)) return;

    $data = xarModAPIFunc('xarcpshop', 'admin', 'menu');
    // Initialise the variable that will hold the items, so that the template
    // doesn't need to be adapted in case of errors
    $data['items'] = array();
    // Specify some labels for display
    $data['nicklabel'] = xarVarPrepForDisplay(xarML('ID:Nickname'));
    $data['namelabel'] = xarVarPrepForDisplay(xarML('www.cafepress.com/'));
    $data['levellabel'] = xarVarPrepForDisplay(xarML('Top Level'));
    $data['optionslabel'] = xarVarPrepForDisplay(xarML('xarCPShop Options'));

    $data['pager'] = xarTplGetPager($startnum,
        xarModAPIFunc('xarcpshop', 'user', 'countitems'),
        xarModURL('xarcpshop', 'admin', 'view', array('startnum' => '%%')),
        xarModGetVar('xarcpshop', 'itemsperpage'));

    if (!xarSecurityCheck('EditxarCPShop')) return;

    $items = xarModAPIFunc('xarcpshop','user','getall',
                     array('startnum' => $startnum,
                           'numitems' => xarModGetVar('xarcpshop','itemsperpage')));
    // Check for exceptions
    if (!isset($items) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; // throw back
    
    $itemnum=count($items);
    for ($i = 0; $i < $itemnum; $i++) {
        $item = $items[$i];
        if (xarSecurityCheck('EditxarCPShop', 0, 'Item', "$item[name]:All:$item[storeid]")) {
            $items[$i]['editurl'] = xarModURL('xarcpshop','admin','modify',
                array('storeid' => $item['storeid']));
        } else {
            $items[$i]['editurl'] = '';
        } 
        $items[$i]['edittitle'] = xarML('Edit');
        if (xarSecurityCheck('DeletexarCPShop', 0, 'Item', "$item[name]:All:$item[storeid]")) {
            $items[$i]['deleteurl'] = xarModURL('xarcpshop',
                'admin',
                'delete',
                array('storeid' => $item['storeid']));
        } else {
            $items[$i]['deleteurl'] = '';
        } 
        $items[$i]['deletetitle'] = xarML('Delete');
    } 
    // Add the array of items to the template variables
    $data['items'] = $items; 

    // Return the template variables defined in this function
    return $data;

} 

?>

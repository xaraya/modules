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
 * view product types
 */
function xarcpshop_admin_prodtypes()
{
    if (!xarVarFetch('startnum', 'str:1:', $startnum, '1', XARVAR_NOT_REQUIRED)) return;

    $data = xarModAPIFunc('xarcpshop', 'admin', 'menu');
    // Initialise the variable that will hold the items, so that the template
    // doesn't need to be adapted in case of errors
    $data['items'] = array();
    // Specify some labels for display
    $data['desclabel'] = xarVarPrepHTMLDisplay(xarML('Product Description'));
    $data['prodtypelabel'] = xarVarPrepHTMLDisplay(xarML('Product Type'));
    $data['prodidlabel'] = xarVarPrepForDisplay(xarML('Product ID'));
    $data['optionslabel'] = xarVarPrepForDisplay(xarML('Product Options'));
    $data['pager'] = xarTplGetPager($startnum,
        xarModAPIFunc('xarcpshop', 'user', 'countprodtypes'),
        xarModURL('xarcpshop', 'admin', 'prodtypes', array('startnum' => '%%')),
        xarModGetVar('xarcpshop', 'itemsperpage'));

    if (!xarSecurityCheck('EditxarCPShop')) return;

    $items = xarModAPIFunc('xarcpshop','user','getallprodtypes',
                     array('startnum' => $startnum,
                           'numitems' => xarModGetVar('xarcpshop','itemsperpage')));
    // Check for exceptions
    if (!isset($items) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; // throw back

    $itemnum=count($items);
    for ($i = 0; $i < $itemnum; $i++) {
        $item = $items[$i];
        if (xarSecurityCheck('EditxarCPShop', 0, 'Item', "$item[prodtype]:All:$item[prodtypeid]")) {
            $items[$i]['editurl'] = xarModURL('xarcpshop','admin','modifyprodtype',
                array('prodtypeid' => $item['prodtypeid']));
        } else {
            $items[$i]['editurl'] = '';
        }
        $items[$i]['edittitle'] = xarML('Edit');
        if (xarSecurityCheck('DeletexarCPShop', 0, 'Item', "$item[prodtype]:All:$item[prodtypeid]")) {
            $items[$i]['deleteurl'] = xarModURL('xarcpshop','admin','deleteprodtype',
                array('prodtypeid' => $item['prodtypeid']));
        } else {
            $items[$i]['deleteurl'] = '';
        }
        $items[$i]['deletetitle'] = xarML('Delete');
    }
    
    if ($itemnum > 0){
       $data['loadlink'] = '';
       $data['removelink'] = xarModURL('xarcpshop','admin','removeproducts');

    }else{
       $data['loadlink'] = xarModURL('xarcpshop','admin','installproducts');
       $data['removelink'] = '';
    }
    // Add the array of items to the template variables
    $data['items'] = $items;

    // Return the template variables defined in this function
    return $data;

}

?>

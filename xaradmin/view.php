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
 * view Dynamic items
 * @return array Data for template
 */
function sigmapersonnel_admin_view()
{
    $data = xarModAPIFunc('sigmapersonnel','admin','menu');

 //   if (!xarVarFetch('catid',    'isset', $catid,    NULL, XARVAR_DONT_SET)) return;
    if (!xarVarFetch('itemtype', 'int:1:', $itemtype, 3, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('startnum', 'int:1:', $startnum, 1, XARVAR_NOT_REQUIRED)) return;

    if (!xarSecurityCheck('EditSIGMAPersonnel')) return;

    $data = xarModAPIFunc('sigmapersonnel', 'admin', 'menu');
    $data['items'] = array();
    $data['itemsperpage'] = xarModGetVar('sigmapersonnel','itemsperpage');
    $data['itemtype'] = $itemtype;
    $data['startnum'] = $startnum;
    // The Generic Menu
    $data['menu']      = xarModFunc('sigmapersonnel','admin','menu');
    $data['menutitle'] = xarVarPrepForDisplay(xarML('View the hooked dynamic data options'));

    if (empty($data['itemtype'])){
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                     'item type', 'admin', 'view', 'sigmapersonnel');
        xarErrorSet(XAR_USER_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return $msg;
    }


    // Return the template variables defined in this function
    return $data;
}

?>

<?php
/**
 * Standard function to view dyn data for this module
 *
 * @package modules
 * @copyright (C) 2005-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Maxercalls module
 * @link http://xaraya.com/index.php/release/247.html
 * @author Maxercalls module development team
 */
/**
 * view dynamic data for maxercalls
 *
 * @author MichelV <michelv@xarayahosting.nl>
 */
function maxercalls_admin_view($args)
{
    extract($args);
    if (!xarVarFetch('itemtype', 'int', $itemtype,  3, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('startnum', 'int:1:', $startnum, '1', XARVAR_NOT_REQUIRED)) return;
//    if (!xarVarFetch('catid', 'int:1:', $catid, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarSecurityCheck('AdminMaxercalls')) return;

    $data['items'] = array();
    // Call the xarTPL helper function to produce a pager in case of there
    // being many items to display.

    // Note that this function includes another user API function.  The
    // function returns a simple count of the total number of items in the item
    // table so that the pager function can do its job properly
    $data['pager'] = xarTplGetPager($startnum,
        xarModAPIFunc('maxercalls', 'user', 'countitems'),
        xarModURL('maxercalls', 'admin', 'view', array('startnum' => '%%')),
        xarModGetVar('maxercalls', 'itemsperpage'));

    $data['itemsperpage'] = xarModGetVar('maxercalls','itemsperpage');
    $data['itemtype'] = $itemtype;
    $data['startnum'] = $startnum;
    // The Generic Menu
    $data['menu']      = xarModFunc('maxercalls','admin','menu');
    $data['menutitle'] = xarVarPrepForDisplay(xarML('View the hooked dynamic data options'));

    if (empty($data['itemtype'])){
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                     'item type', 'admin', 'view', 'maxercalls');
        xarErrorSet(XAR_USER_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return $msg;
    }
    // Return the template variables defined in this function
    return $data;
}
?>

<?php
/**
 * File: $Id:
 * 
 * xarCPShop function to modify configuration parameters
 *
 * @copyright (C) 2004 by Jo Dalle Nogare
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://xaraya.athomeandabout.com
 *
 * @subpackage xarcpshop
 * @author jojodee@xaraya.com
 */
/**
 * This is a standard function to update the configuration parameters of the
 * module given the information passed back by the modification form
 */
function xarcpshop_admin_updateconfig()
{
    if (!xarVarFetch('itemsperpage', 'int', $itemsperpage, 10, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('shorturls', 'checkbox', $shorturls, false, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('closed', 'checkbox', $closed, false, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('cpdown', 'checkbox', $cpdown, false, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('breadcrumb', 'checkbox', $breadcrumb, false, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('litemode', 'checkbox', $litemode, false, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('defaultstore', 'str:1:', $defaultstore, false, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('cart', 'str:1:', $cart, false, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('localimages', 'str:1:', $localimages, false, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('verbose', 'checkbox', $verbose, false, XARVAR_NOT_REQUIRED)) return;

    if (!xarSecConfirmAuthKey()) return;


    xarModSetVar('xarcpshop', 'itemsperpage', $itemsperpage);
    xarModSetVar('xarcpshop', 'SupportShortURLs', $shorturls);
    xarModSetVar('xarcpshop', 'closed', $closed);
    xarModSetVar('xarcpshop', 'cpdown', $cpdown);
    xarModSetVar('xarcpshop', 'breadcrumb', $breadcrumb);
    xarModSetVar('xarcpshop', 'litemode', $litemode);
    xarModSetVar('xarcpshop', 'defaultstore', $defaultstore);
    xarModSetVar('xarcpshop', 'localimages', $localimages);
    xarModSetVar('xarcpshop', 'cart', $cart);
    xarModSetVar('xarcpshop', 'verbose', $verbose);

    xarModCallHooks('module','updateconfig','xarcpshop',
                   array('module' => 'xarcpshop'));

    // This function generated no output, and so now it is complete we redirect
    // the user to an appropriate page for them to carry on their work
    xarResponseRedirect(xarModURL('xarcpshop', 'admin', 'modifyconfig'));

    // Return
    return true;
}

?>

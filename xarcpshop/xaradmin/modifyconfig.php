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
 * This is a standard function to modify the configuration parameters of the
 * module
 */
function xarcpshop_admin_modifyconfig()
{ 
    $data = xarModAPIFunc('xarcpshop', 'admin', 'menu');
    // Security check - important to do this as early as possible to avoid
    // potential security holes or just too much wasted processing
    if (!xarSecurityCheck('AdminxarCPShop')) return;
    // Generate a one-time authorisation code for this operation
    $data['authid'] = xarSecGenAuthKey();
    $data['itemsvalue'] = xarModGetVar('xarcpshop', 'itemsperpage');
    $data['updatebutton'] = xarVarPrepForDisplay(xarML('Update Configuration'));
    $data['closedchecked'] = xarModGetVar('xarcpshop', 'closed')? 'checked' : '';
    $data['cpdownchecked'] = xarModGetVar('xarcpshop', 'cpdown')? 'checked' : '';
    $data['defaultstore'] = xarModGetVar('xarcpshop', 'defaultstore');
    $data['localimages'] = xarModGetVar('xarcpshop', 'localimages');
    $data['cart'] = xarModGetVar('xarcpshop', 'cart');
    $data['breadcrumbchecked'] = xarModGetVar('xarcpshop', 'breadcrumb')? 'checked' : '';
    $data['litemodechecked'] = xarModGetVar('xarcpshop', 'litemode') ? 'checked' : '';
    $data['verbosechecked'] = xarModGetVar('xarcpshop', 'verbose')? 'checked' : '';
    $data['sectionthumbmaxsize'] = xarModGetVar('xarcpshop', 'sectionthumbmaxsize');

    $data['shorturlslabel'] = xarML('Enable short URLs?');
    $data['shorturlschecked'] = xarModGetVar('xarcpshop', 'SupportShortURLs') ? 'checked' : '';

    $hooks = xarModCallHooks('module', 'modifyconfig', 'xarcpshop',
        array('module' => 'xarcpshop'));
    if (empty($hooks)) {
        $data['hooks'] = '';
    } elseif (is_array($hooks)) {
        $data['hooks'] = join('', $hooks);
    } else {
        $data['hooks'] = $hooks;
    } 
    // Return the template variables defined in this function
    return $data;
} 

?>

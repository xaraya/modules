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
 * add new shop
 * This is a standard function that is called whenever an administrator
 * wishes to create a new module item
 */
function xarcpshop_admin_new($args)
{
    extract($args);

    if (!xarVarFetch('section', 'str:1:', $section, $section,XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('name', 'str:1:', $name, $name, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('invalid', 'str:1:', $invalid, $invalid, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('nickname', 'str:1:', $nickname, $nickname, XARVAR_NOT_REQUIRED)) return;

    $data = xarModAPIFunc('xarcpshop', 'admin', 'menu');
    // Security check - important to do this as early as possible to avoid
    // potential security holes or just too much wasted processing
    if (!xarSecurityCheck('AddxarCPShop')) return;
    // Generate a one-time authorisation code for this operation
    $data['authid'] = xarSecGenAuthKey();
    $data['invalid'] = $invalid;
    // Specify some labels for display
    $data['addbutton'] = xarVarPrepForDisplay(xarML('Add Store'));

    $item = array();
    $item['module'] = 'xarcpshop';
    $hooks = xarModCallHooks('item', 'new', '', $item);

    if (empty($hooks)) {
        $data['hooks'] = '';
    } elseif (is_array($hooks)) {
        $data['hooks'] = join('', $hooks);
    } else {
        $data['hooks'] = $hooks;
    }
    // For E_ALL purposes, we need to check to make sure the vars are set.
    // If they are not set, then we need to set them empty to surpress errors
    if (empty($nickname)) {
        $data['nickname'] = '';
    } else {
        $data['nickname'] = $nickname;
    }

    if (empty($name)) {
        $data['name'] = '';
    } else {
        $data['name'] = $name;
    }
    if (empty($toplevel)) {
        $data['toplevel'] = '';
    } else {
        $data['toplevel'] = $toplevel;
    }
    // Return the template variables defined in this function
    return $data;
}

?>

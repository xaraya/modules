<?php
/**
 * Create a Dyn Data item from SIGMAPersonnel
 *
 * @package modules
 * @copyright (C) 2005-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Sigmapersonnel Module
 * @link http://xaraya.com/index.php/release/418.html
 * @author SIGMAPersonnel Module Development Team
 */
/**
 * add new dd item
 * @author MichelV <michelv@xarayahosting.nl>
 */
function sigmapersonnel_admin_new()
{
    if (!xarVarFetch('itemtype', 'int:1:', $itemtype, '', XARVAR_GET_OR_POST)) return;
    if (!xarVarFetch('preview',  'str::',  $preview,  '', XARVAR_NOT_REQUIRED)) return;

    if (empty($itemtype)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                     'item type', 'admin', 'new', 'sigmapersonnel');
        xarErrorSet(XAR_USER_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return $msg;
    }

    $data = xarModAPIFunc('sigmapersonnel','admin','menu');

    if (!xarSecurityCheck('AdminSIGMAPersonnel')) return;

    // get the Dynamic Object defined for this module (and itemtype, if relevant)
    $data['object'] = xarModAPIFunc('dynamicdata','user','getobject',
                                     array('module' => 'sigmapersonnel',
                                           'itemtype' => $itemtype));
    $data['itemtype'] = $itemtype;
    $data['preview'] = $preview;

    $data['menutitle'] = xarVarPrepForDisplay(xarML('Make a new hooked dynamic data object'));
    $item = array();
    $item['module'] = 'sigmapersonnel';
    $hooks = xarModCallHooks('item','new','',$item);
    if (empty($hooks)) {
        $data['hooks'] = '';
    } elseif (is_array($hooks)) {
        $data['hooks'] = join('',$hooks);
    } else {
        $data['hooks'] = $hooks;
    }
    // Authentication
    $data['authid'] =xarSecGenAuthKey();
    // Return the template variables defined in this function
    return $data;
}

?>

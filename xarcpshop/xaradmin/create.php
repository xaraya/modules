<?php
/**
 * File: $Id: 
 * 
 * Standard function to create a new item
 * 
 * @copyright (C) 2004 by Jo Dalle Nogare
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://xaraya.athomeandabout.com
 *
 * @subpackage xarcpshop
 * @author jojodee@xaraya.com
 */

/**
 * @param  $ 'name' the name of the shopto be created
 * @param  $ 'sid' the number of the shop to be created
 */
function xarcpshop_admin_create($args)
{ 
     extract($args);

    if (!xarVarFetch('toplevel', 'str:1:', $toplevel, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('objectid', 'str:1:', $objectid, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('invalid', 'str:1:', $invalid, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('name', 'str:1:', $name, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('nickname', 'str:1:', $nickname, '', XARVAR_NOT_REQUIRED)) return;
    $item = xarModAPIFunc('xarcpshop',
                          'user',
                          'validateitem',
                          array('name' => $name));

    // Argument check
    $invalid = array();
    if (empty($nickname) || !is_string($nickname)) {
        $invalid['nickname'] = 1;
    }
    if (empty($name) || !is_string($name)) {
        $invalid['name'] = 1;

    }

    // check if we have any errors
    if (count($invalid) > 0) {
        return xarModFunc('xarcpshop', 'admin', 'new',
                             array('nickname' => $nickname,
                                    'name' => $name,
                                    'toplevel' => $toplevel,
                                    'invalid' => $invalid));
    }
    if (!xarSecConfirmAuthKey()) return;

    $sid = xarModAPIFunc('xarcpshop',
                            'admin',
                            'create',
                          array('name' => $name,
                                'nickname' => $nickname,
                                'toplevel' => $toplevel));

   if (!isset($sid) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; // throw back

     xarResponseRedirect(xarModURL('xarcpshop', 'admin', 'view'));
    // Return
    return true;
} 

?>

<?php
/**
 * Window Module Update Configuration
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Window Module
 * @link http://xaraya.com/index.php/release/3002.html
 * @author Window Module Development Team
 */
 
/*
 * Modify the general configuration settings in Window module
 */

function window_admin_updateconfig($var)
{

    if (!xarVarFetch('tab', 'str:1:100', $tab, 'general', XARVAR_NOT_REQUIRED)) return;

    // Confirm authorisation code
    if (!xarSecConfirmAuthKey()) return;
    switch ($tab) {
        case 'general':
            if (!xarVarFetch('default_size',     'int', $default_size, 0, XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('allow_local_only', 'int', $allow_local_only, 0, XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('use_buffering',    'int', $use_buffering, 0, XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('no_user_entry',    'int', $no_user_entry, 0, XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('security',         'int', $security, 0, XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('reg_user_only',    'int', $reg_user_only, 0, XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('open_direct',      'int', $open_direct, 0, XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('use_fixed_title',  'int', $use_fixed_title, 0, XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('auto_resize',      'int', $auto_resize, 0, XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('vsize',            'int', $vsize, 600, XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('hsize',            'str', $hsize, '100%', XARVAR_NOT_REQUIRED)) return;

            //Save Settings
            xarModSetVar('window', 'default_size', $default_size);
            xarModSetVar('window', 'allow_local_only', $allow_local_only);
            xarModSetVar('window', 'use_buffering', $use_buffering);
            xarModSetVar('window', 'no_user_entry', $no_user_entry);
            xarModSetVar('window', 'security', $security);
            xarModSetVar('window', 'reg_user_only', $reg_user_only);
            xarModSetVar('window', 'open_direct', $open_direct);
            xarModSetVar('window', 'use_fixed_title', $use_fixed_title);
            xarModSetVar('window', 'auto_resize', $auto_resize);
            xarModSetVar('window', 'vsize', $vsize);
            xarModSetVar('window', 'hsize', $hsize);
            break;
        case 'display':
            $windowid=xarModGetIDFromName('window');
            if (!xarVarFetch('showusermenu', 'checkbox', $showusermenu, true, XARVAR_NOT_REQUIRED)) return;
            $usermenu = $showusermenu ? 1:0;
            $updated = xarModAPIFunc('modules','admin','updateproperties',
                                      array('regid' => $windowid,
                                            'usercapable'  => $usermenu));
            if (!xarVarFetch('use_iframe', 'checkbox', $use_iframe, true, XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('use_object', 'checkbox', $use_object, true, XARVAR_NOT_REQUIRED)) return;

            xarModSetVar('window', 'use_iframe',  $use_iframe
);
            xarModSetVar('window', 'use_object', $use_object);

            break;
        case 'tab3':
            break;
        default:
            break;
    }

    xarResponseRedirect(xarModURL('window', 'admin', 'modifyconfig',array('tab' => $tab)));
    return true;

}
?>

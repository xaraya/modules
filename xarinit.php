<?php
/**
 * Xaraya wrapper module for DotProject: initialise
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage xarDPLink Module
 * @link http://xaraya.com/index.php/release/591.html
 * @author xarDPLink Module Development Team
 */
function xardplink_init()
{
    xarModSetVar('xardplink', 'url',  '/dotproject');
    xarModSetVar('xardplink', 'use_window', 1);
    xarModSetVar('xardplink', 'use_wrap', 0);

    /**
     * Register the module components that are privileges objects
     * Format is
     * xarregisterMask(Name,Realm,Module,Component,Instance,Level,Description)
     */

    xarRegisterMask('ViewXardplink', 'All', 'xardplink', 'Item', 'All:All:All', 'ACCESS_OVERVIEW');
    xarRegisterMask('ReadXardplink', 'All', 'xardplink', 'Item', 'All:All:All', 'ACCESS_READ');
    xarRegisterMask('EditXardplink', 'All', 'xardplink', 'Item', 'All:All:All', 'ACCESS_EDIT');
    xarRegisterMask('AddXardplink', 'All', 'xardplink', 'Item', 'All:All:All', 'ACCESS_ADD');
    xarRegisterMask('DeleteXardplink', 'All', 'xardplink', 'Item', 'All:All:All', 'ACCESS_DELETE');
    xarRegisterMask('AdminXardplink', 'All', 'xardplink', 'Item', 'All:All:All', 'ACCESS_ADMIN');

    return true;
}
/**
 * Upgrade from previous version
 */
function xardplink_upgrade($oldversion)
{
    switch ($oldversion) {
        case '0.5.0':
        case '0.7.0':
            xarModDelAllVars('xardplink');
            xarModSetVar('xardplink', 'use_wrap', 0);
            xarModSetVar('xardplink', 'use_window', 1);
            xarRemoveMasks('xardplink');
            xarRegisterMask('ViewXardplink', 'All', 'xardplink', 'Item', 'All:All:All', 'ACCESS_OVERVIEW');
            xarRegisterMask('ReadXardplink', 'All', 'xardplink', 'Item', 'All:All:All', 'ACCESS_READ');
            xarRegisterMask('EditXardplink', 'All', 'xardplink', 'Item', 'All:All:All', 'ACCESS_EDIT');
            xarRegisterMask('AddXardplink', 'All', 'xardplink', 'Item', 'All:All:All', 'ACCESS_ADD');
            xarRegisterMask('DeleteXardplink', 'All', 'xardplink', 'Item', 'All:All:All', 'ACCESS_DELETE');
            xarRegisterMask('AdminXardplink', 'All', 'xardplink', 'Item', 'All:All:All', 'ACCESS_ADMIN');
            return xardplink_upgrade('0.8.0');
        case '0.8.0':
            break;
    }
    return true;
}
/**
 * Delete this module
 */
function xardplink_delete()
{
    xarModDelAllVars('xardplink');
    xarRemoveMasks('xardplink');
    return true;
}
?>

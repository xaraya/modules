<?php
/**
 * Standard Utility function pass individual menu items to the main menu
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
    xarModSetVar('xardplink', 'use_window', 0);
    xarModSetVar('xardplink', 'use_postwrap', 0);

    /**
     * Register the module components that are privileges objects
     * Format is
     * xarregisterMask(Name,Realm,Module,Component,Instance,Level,Description)
     */

    xarRegisterMask('ViewXardplink', 'All', 'example', 'Item', 'All:All:All', 'ACCESS_OVERVIEW');
    xarRegisterMask('ReadXardplink', 'All', 'example', 'Item', 'All:All:All', 'ACCESS_READ');
    xarRegisterMask('EditXardplink', 'All', 'example', 'Item', 'All:All:All', 'ACCESS_EDIT');
    xarRegisterMask('AddXardplink', 'All', 'example', 'Item', 'All:All:All', 'ACCESS_ADD');
    xarRegisterMask('DeleteXardplink', 'All', 'example', 'Item', 'All:All:All', 'ACCESS_DELETE');
    xarRegisterMask('AdminXardplink', 'All', 'example', 'Item', 'All:All:All', 'ACCESS_ADMIN');

    return true;
}

function xardplink_upgrade($oldversion)
{
    switch ($oldversion) {
        case '0.5.0':
            break;

    }
    return true;
}

function xardplink_delete()
{
    xarModDelAllVars('xardplink');
    return true;
}
?>

<?php
/**
 * HTML Module
 *
 * @package modules
 * @subpackage html module
 * @category Third Party Xaraya Module
 * @version 1.5.0
 * @copyright see the html/credits.html file in this release
 * @link http://www.xaraya.com/index.php/release/779.html
 * @author John Cox
 */

/**
 * modify configuration
 */
function html_admin_modifyconfig()
{
    // Security Check
    if (!xarSecurity::check('AdminHTML')) {
        return;
    }

    if (!xarVar::fetch('phase', 'str:1:100', $phase, 'modify', xarVar::NOT_REQUIRED)) {
        return;
    }

    switch (strtolower($phase)) {
        case 'modify':
        default:
            //Set Data Array
            $data                   = [];
            $data['authid']         = xarSec::genAuthKey();
            $data['submitlabel']    = xarML('Submit');

            // Call Modify Config Hooks
            $hooks = xarModHooks::call(
                'module',
                'modifyconfig',
                'html',
                ['module'     => 'html',
                                           'itemtype'   => 0, ]
            );

            if (empty($hooks)) {
                $hooks = [];
            }
            $data['hooks'] = $hooks;
            break;

        case 'update':
            if (!xarVar::fetch('dolinebreak', 'checkbox', $dolinebreak, false, xarVar::NOT_REQUIRED)) {
                return;
            }
            if (!xarVar::fetch('dobreak', 'checkbox', $dobreak, false, xarVar::NOT_REQUIRED)) {
                return;
            }
            if (!xarVar::fetch('transformtype', 'int', $transformtype, 1)) {
                return;
            }
            // Confirm authorisation code
            if (!xarSec::confirmAuthKey()) {
                return;
            }
            // Update module variables
            xarModVars::set('html', 'dolinebreak', $dolinebreak);
            xarModVars::set('html', 'dobreak', $dobreak);
            xarModVars::set('html', 'transformtype', $transformtype);
            // Call Update Config Hooks
            xarModHooks::call(
                'module',
                'updateconfig',
                'html',
                ['module'      => 'html',
                                  'itemtype'    => 0, ]
            );

            xarController::redirect(xarController::URL('html', 'admin', 'modifyconfig'));
            // Return
            return true;
            break;
    }
    return $data;
}

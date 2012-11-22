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
    if (!xarSecurityCheck('AdminHTML')) return;

    if (!xarVarFetch('phase', 'str:1:100', $phase, 'modify', XARVAR_NOT_REQUIRED)) return;

    switch (strtolower($phase)) {
        case 'modify':
        default:
            //Set Data Array
            $data                   = array();
            $data['authid']         = xarSecGenAuthKey();
            $data['submitlabel']    = xarML('Submit');

            // Call Modify Config Hooks
            $hooks = xarModCallHooks('module',
                                     'modifyconfig',
                                     'html',
                                     array('module'     => 'html',
                                           'itemtype'   => 0));

            if (empty($hooks)) {
                $hooks = array();
            }
            $data['hooks'] = $hooks;
            break;

        case 'update':
            if (!xarVarFetch('dolinebreak', 'checkbox', $dolinebreak, false, XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('dobreak', 'checkbox', $dobreak, false, XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('transformtype', 'int', $transformtype, 1)) return;
            // Confirm authorisation code
            if (!xarSecConfirmAuthKey()) return;
            // Update module variables
            xarModVars::set('html', 'dolinebreak', $dolinebreak);
            xarModVars::set('html', 'dobreak', $dobreak);
            xarModVars::set('html', 'transformtype', $transformtype);
            // Call Update Config Hooks
            xarModCallHooks('module',
                            'updateconfig',
                            'html',
                            array('module'      => 'html',
                                  'itemtype'    => 0));

            xarController::redirect(xarModURL('html', 'admin', 'modifyconfig'));
            // Return
            return true;
            break;
    }
    return $data;
}
?>
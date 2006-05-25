<?php
/**
 * Xaraya HTML Module
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage HTML Module
 * @link http://xaraya.com/index.php/release/779.html
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
            xarModSetVar('html', 'dolinebreak', $dolinebreak);
            xarModSetVar('html', 'dobreak', $dobreak);            
            xarModSetVar('html', 'transformtype', $transformtype);
            // Call Update Config Hooks
            xarModCallHooks('module', 
                            'updateconfig', 
                            'html',
                            array('module'      => 'html', 
                                  'itemtype'    => 0));

            xarResponseRedirect(xarModURL('html', 'admin', 'modifyconfig')); 
            // Return
            return true;
            break;
    } 
    return $data;
} 
?>
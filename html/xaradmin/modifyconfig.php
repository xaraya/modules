<?php
/**
 * File: $Id$
 *
 * Modify configuration
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.com
 * @subpackage html
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
            if (!xarVarFetch('transformtype', 'int', $transformtype, 1)) return;
            // Confirm authorisation code
            if (!xarSecConfirmAuthKey()) return; 
            // Update module variables
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
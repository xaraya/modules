<?php
/**
 * File: $Id$
 * 
 * Modify xarBB Configuration
 * 
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.org
 *
 * @subpackage  xarbb Module
 * @author John Cox
*/

/**
 * modify configuration
 */
function xarbb_admin_modifyconfig()
{
    // Security Check
    if(!xarSecurityCheck('AdminxarBB')) return;

    if (!xarVarFetch('phase', 'str:1:100', $phase, 'modify', XARVAR_NOT_REQUIRED)) return;

    switch(strtolower($phase)) {

        case 'modify':
        default:

            $hooks = xarModCallHooks('module', 'modifyconfig', 'xarbb',
                                    array('module' => 'xarbb',
                                          'itemtype' => 0)); // forum
            if (empty($hooks)) {
                $data['hooks'] = '';
            } elseif (is_array($hooks)) {
                $data['hooks'] = join('',$hooks);
            } else {
                $data['hooks'] = $hooks;
            }

            $data['authid'] = xarSecGenAuthKey();

            break; 

        case 'update':

            if (!xarVarFetch('hottopic','int:1:',$hotTopic,10,XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('postsperpage','int:1:',$postsperpage,20,XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('topicsperpage','int:1:',$topicsperpage,20,XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('forumsperpage','int:1:',$forumsperpage,20,XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('supportshorturls','checkbox', $supportshorturls,false,XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('allowhtml','checkbox', $allowhtml,false,XARVAR_NOT_REQUIRED)) return;
            // Confirm authorisation code
            if (!xarSecConfirmAuthKey()) return;

            // Update module variables
            xarModSetVar('xarbb', 'hottopic', $hotTopic);
            xarModSetVar('xarbb', 'allowhtml', $allowhtml);
            xarModSetVar('xarbb', 'topicsperpage', $topicsperpage);
            xarModSetVar('xarbb', 'postsperpage', $postsperpage);
            xarModSetVar('xarbb', 'forumsperpage', $forumsperpage);
            xarModSetVar('xarbb', 'SupportShortURLs', $supportshorturls);
            xarModCallHooks('module','updateconfig','xarbb',
                           array('module' => 'xarbb',
                                 'itemtype' => 0)); // General forum hooks
            xarResponseRedirect(xarModURL('xarbb', 'admin', 'modifyconfig'));

            // Return
            return true;

            break;
    }

    return $data;
}
?>

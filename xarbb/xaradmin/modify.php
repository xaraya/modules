<?php
/**
 * File: $Id$
 * 
 * Xaraya Modify an existing forum
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
 * @author John Cox
 * @function to modify an existing forum
*/
function xarbb_admin_modify()
{
    // Get parameters
    if (!xarVarFetch('fid','id', $fid)) return;
	if (!xarVarFetch('phase', 'str:1:', $phase, 'form', XARVAR_NOT_REQUIRED, XARVAR_PREP_FOR_DISPLAY)) return;
    switch(strtolower($phase)) {

        case 'form':
        default:
            
            // The user API function is called.
	    $data = xarModAPIFunc('xarbb',
	                          'user',
	                          'getforum',
	                          array('fid' => $fid));
	
	    if (empty($data)) return;
		
            // Security Check
            if(!xarSecurityCheck('EditxarBB',1,'Forum',$data['catid'].':'.$data['fid'])) return;

            $data['module'] = 'xarbb';
            $data['itemtype'] = 1; // forum
            $data['itemid'] = $fid;
            $hooks = xarModCallHooks('item','modify',$fid,$data);
            if (empty($hooks)) {
                $data['hooks'] = '';
            } elseif (is_array($hooks)) {
                $data['hooks'] = join('',$hooks);
            } else {
                $data['hooks'] = $hooks;
            }

            //Load Template
            $data['authid'] = xarSecGenAuthKey();
            $data['createlabel'] = xarML('Submit');
            break;

        case 'update':

            if (!xarVarFetch('fname', 'str:1:', $fname, '', XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('fdesc', 'str:1:', $fdesc, '', XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('fstatus','int', $fstatus, 0)) return;

            // Confirm authorisation code.
            if (!xarSecConfirmAuthKey()) return;

            // The API function is called.
            if(!xarModAPIFunc('xarbb',
                              'admin',
                              'update',
                               array('fid'      => $fid,
                                     'fname'    => $fname,
                                     'fdesc'    => $fdesc,
                                     'fstatus'  => $fstatus))) return;

            // Redirect
            xarResponseRedirect(xarModURL('xarbb', 'admin', 'view'));

            break;
    }
	return $data;
}
?>
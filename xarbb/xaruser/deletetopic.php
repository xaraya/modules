<?php
/**
 * File: $Id$
 * 
 * Delete a forum topic
 * 
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.org
 *
 * @subpackage  xarbb Module
 * @author John Cox
*/
function xarbb_user_deletetopic()
{
    // Get parameters
    if (!xarVarFetch('tid','int:1:',$tid)) return;
    if (!xarVarFetch('obid','str:1:',$obid,$tid,XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('confirmation','int',$confirmation,'',XARVAR_NOT_REQUIRED)) return;

	// for sec check
    if(!$topic = xarModAPIFunc('xarbb','user','gettopic',array('tid' => $tid))) return;

    // Security Check
    if(!xarSecurityCheck('ModxarBB',1,'Forum',$topic['catid'].':'.$topic['fid'])) return;

    // Check for confirmation.
    if (empty($confirmation)) {
        //Load Template
        $data['authid'] = xarSecGenAuthKey();
        $data['tid'] = $tid;
        return $data;
    }

    // If we get here it means that the user has confirmed the action

    // Confirm authorisation code.
    if (!xarSecConfirmAuthKey()) return;

    if (!xarModAPIFunc('xarbb',
		               'admin',
		               'deletetopics',
                        array('tid' => $tid))) return;

    $tposter = xarUserGetVar('uid');

    if (!xarModAPIFunc('xarbb',
                       'user',
                       'updateforumview',
                       array('fid'      => $topic['fid'],
                             'deletetopic'  => 1,
                             'fposter'  => $tposter))) return;

    // Redirect
    xarResponseRedirect(xarModURL('xarbb', 'user', 'viewforum',array("fid" => $topic['fid'])));

    // Return
    return true;
}

?>

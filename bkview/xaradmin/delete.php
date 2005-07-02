<?php


/**
 * File: $Id$
 *
 * Delete a repository entry
 *
 * @package modules
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @link http://www.xaraya.com
 * 
 * @subpackage bkview
 * @author Marcel van der Boom <marcel@xaraya.com>
*/

/**
 * delete item
 */
function bkview_admin_delete($args)
{
    xarVarFetch('repoid','id',$repoid);
    xarVarFetch('confirm','bool',$confirm,false);
	extract($args);
	
	$item = xarModAPIFunc('bkview',	'user','get', array('repoid' => $repoid));
	if (!isset($item) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; // throw back
	
	if (!xarSecurityCheck('AdminAllRepositories')) return;
	
	// Check for confirmation.
	if (!$confirm) {
		$data['repoid'] = $repoid;
		
		// Add some other data you'll want to display in the template
		$data['repoidvalue']= xarVarPrepForDisplay($item['repoid']);
		$data['reponamevalue'] = xarVarPrepForDisplay($item['reponame']);
		$data['confirmbutton'] = xarML('Confirm');
		$data['pageinfo']=xarML('Delete a repository');
		// Generate a one-time authorisation code for this operation
		$data['authid'] = xarSecGenAuthKey();
		return $data;
	}
	
    if (!xarSecConfirmAuthKey()) return;
		
	if (!xarModAPIFunc('bkview', 'admin','delete', array('repoid' => $repoid))) {
		return; // throw back
	}
	xarSessionSetVar('statusmsg', xarML('Repository unregistered'));
	xarResponseRedirect(xarModURL('bkview', 'admin', 'view'));
	return true;
}

?>
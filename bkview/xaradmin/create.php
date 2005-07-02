<?php


/**
 * File: $Id$
 *
 * create new repository entry
 *
 * @package modules
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @link http://www.xaraya.com
 * 
 * @subpackage bkview
 * @author Marcel van der Boom <marcel@xaraya.com>
*/

/**
 * This is a standard function that is called with the results of the
 * form supplied by bkview_admin_new() to create a new item
 * @param 'name' the name of the item to be created
 * @param 'number' the number of the item to be created
 */
function bkview_admin_create($args)
{
    xarVarFetch('reponame','str::',$reponame);
    xarVarFetch('repopath','str::',$repopath);
	extract($args);
	
    if (!xarSecConfirmAuthKey()) return;
	
    $repoid = xarModAPIFunc('bkview','admin','create',array('reponame' => $reponame,'repopath' => $repopath));
	if (!isset($repoid) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; // throw back
	
	xarSessionSetVar('statusmsg', xarML('Repository registered'));
	xarResponseRedirect(xarModURL('bkview', 'admin', 'view'));
	return true;
}
?>
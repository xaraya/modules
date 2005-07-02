<?php


/**
 * File: $Id$
 *
 * modify repository entry
 *
 * @package modules
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @link http://www.xaraya.com
 * 
 * @subpackage bkview
 * @author Marcel van der Boom <marcel@xaraya.com>
*/

/**
 * modify an item
 * This is a standard function that is called whenever an administrator
 * wishes to modify a current module item
 * @param 'repoid' the id of the item to be modified
 */
function bkview_admin_modify($args)
{
    xarVarFetch('repoid','id',$repoid,NULL);
   	extract($args);

	$item = xarModAPIFunc('bkview','user','get',array('repoid' => $repoid));
	if (!isset($item) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; // throw back
	
	// Security check
	if (!xarSecurityCheck('AdminAllRepositories')) return;
	
	$item['module'] = 'bkview';
	$hooks = xarModCallHooks('item','modify',$repoid,$item);
	if (empty($hooks)) {
            $hooks = '';
        } elseif (is_array($hooks)) {
            $hooks = join('',$hooks);
	}
	
	// Return the template variables defined in this function
	$data['authid']= xarSecGenAuthKey();
	$data['submitbutton'] = xarVarPrepForDisplay(xarML('Update repository'));
	$data['hooks'] = $hooks;
    $data['repoid'] = $repoid;
	$data['reponame'] = $item['reponame'];
    $data['repopath'] = $item['repopath'];
    $data['pageinfo'] = xarML('Modify repository');
    return $data;
}

?>
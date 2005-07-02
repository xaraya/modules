<?php


/**
 * File: $Id$
 *
 * Short description of purpose of file
 *
 * @package modules
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @link http://www.xaraya.com
 * 
 * @subpackage module name
 * @author Marcel van der Boom <marcel@xaraya.com>
*/

/**
 * view repositories
 */
function bkview_admin_view()
{
	$data['items'] = array();

	// Security check
	if (!xarSecurityCheck('AdminAllRepositories')) return;
	
	$items = xarModAPIFunc('bkview', 'user', 'getall',array());
	if (!isset($item) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; // throw back
	
	// TODO: Check individual permissions for Edit / Delete
	for ($i = 0; $i < count($items); $i++) {
		$item = $items[$i];
        $items[$i]['editurl'] = xarModURL('bkview','admin','modify',
                                              array('repoid' => $item['repoid']));
        $items[$i]['edittitle'] = xarML('Edit');
        $items[$i]['deleteurl'] = xarModURL('bkview',	'admin','delete',
                                                array('repoid' => $item['repoid']));
        $items[$i]['deletetitle'] = xarML('Delete');
	}
	
	// Add the array of items to the template variables
	$data['items'] = $items;
    $data['pageinfo']=xarML('View registered repositories');
	return $data;
}

?>
<?php

/**
 * File: $Id$
 *
 * Build administrative menu links
 *
 * @package modules
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @link http://www.xaraya.com
 * 
 * @subpackage bloggerapi
 * @author Marcel van der Boom <marcel@xaraya.com>
*/




/**
 * utility function pass individual menu items to the main menu
 *
 * @author Marcel van der Boom <marcel@xaraya.com>
 * @returns array
 * @return array containing the menulinks for the main menu items.
 */
function bloggerapi_adminapi_getmenulinks()
{
	// Security Check
	if (xarSecurityCheck('AdminBloggerAPI',0)) {
		$menulinks[] = Array('url'   => xarModURL('bloggerapi','admin','modifyconfig'),
												 'title' => xarML('Modify the configuration of the Bloggerapi module'),
												 'label' => xarML('Modify Config'));
	}
	
	if (empty($menulinks)){
		$menulinks = '';
	}
	
	return $menulinks;
}
?>
<?php
/**
 * Add a record for a file 
 *
 * @package modules
 * @copyright (C) 2002-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage downloads
 * @link http://www.xaraya.com/index.php/release/19741.html
 * @author potion <ryan@webcommunicate.net>
 */
/**
 * Add a record for a file 
 */
function downloads_adminapi_addrecord($args)
{

	extract($args);

    sys::import('modules.dynamicdata.class.objects.master');
	sys::import('modules.downloads.xarproperties.upload');

	$object = DataObjectMaster::getObject(array('name' => 'downloads'));

	$object->properties['filename']->initialization_basedirectory = $location;
	$object->properties['filename']->setValue($filename);
	$object->properties['location']->setValue($location);
	// We want some spaces in titles so the admin view doesn't get distorted
	$filename = str_replace('_',' ',$filename);
	$filename = str_replace('-',' ',$filename);
	$filename = str_replace('.',' ',$filename);
	$object->properties['title']->setValue($filename);
	$object->properties['status']->setValue($status);

	$itemid = $object->createItem();

	return true;

}

?>
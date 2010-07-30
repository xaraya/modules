<?php
/**
 * Get info for a record & its file
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
 * Get info for a record & its file
 */

function downloads_userapi_getrecordfileinfo($args) {

	$decimals = 2;

	extract($args);

	sys::import('modules.dynamicdata.class.objects.master');

	$object = DataObjectMaster::getObject(array('name' => 'downloads'));
	$object->getItem(array('itemid' => $itemid));
	$title = $object->properties['title']->getValue();
	$description = $object->properties['description']->getValue();
	$filename = $object->properties['filename']->getValue();
	$directory = $object->properties['directory']->getValue();
	$status = $object->properties['status']->getValue();
	$basepath = xarMod::apiFunc('downloads','admin','getbasepath');

	$filepath = $basepath . $directory . '/' . $filename;
	
	if (file_exists($filepath)) {
		$bytes = filesize($filepath);
		$size = round($bytes/1048576, $decimals); // megabytes
	} else {
		$size = false;
	}

	return array(
		'filename' => $filename, 
		'title' => $title, 
		'description' => $description, 
		'directory' => $directory, 
		'filesize' => $size,  // megabytes
		'status' => $status
		);

}

?>
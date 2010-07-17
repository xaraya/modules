<?php
/**
 * Get full paths to file directories
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
 * get full paths to file directories
 */

function downloads_userapi_getlocations() {

	$dirs = xarModVars::get('downloads', 'file_directories');

	$basepath = xarMod::apiFunc('downloads','admin','getbasepath');

	$dirs = str_replace(' ',"\r",$dirs);
	$dirs = str_replace(',',"\r",$dirs);
	$dirs = str_replace('\n',"\r",$dirs);

	// Make it harder to traverse directories
	$dirs = str_replace('/','',$dirs);
	$dirs = str_replace('.','',$dirs);
	$dirs = str_replace('\\','',$dirs);
	
	$dirs = explode("\r",$dirs);
	
	foreach ($dirs as $key=>$value) {
		
		$value = $basepath . trim($value);
		if (strlen($value) > 0) {
			$locations[$value] = $value;
		}
	} 

	return $locations;

}

?>
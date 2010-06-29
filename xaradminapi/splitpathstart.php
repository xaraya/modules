<?php
/**
 * Split a path into two parts:  pathstart, and the rest
 *
 * @package modules
 * @copyright (C) 2002-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Path Module
 * @link http://www.xaraya.com/index.php/release/eid/1150
 * @author potion <ryan@webcommunicate.net>
 */
/**
 * Split a path into two parts:  pathstart, and the rest

 * @param $args['path'] required string the path
 */
function path_adminapi_splitpathstart($args) {

	extract($args);

	$path = substr($path, 1);
	$pos = strpos($path, '/');
	if($pos) {
		$pathstart = substr($path, 0, $pos);
		$remainder = str_replace($pathstart . '/','',$path);
		$slash = '/';
	} else {
		$pathstart = $path;
		$remainder = '';
		$slash = '';
	}

	return array($pathstart,$slash,$remainder);

}
?>

<?php
/**
 * Get an action for a path
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
 *  get an action for a path
 * @param args[$path] required string path
 */
function path_userapi_path2action($args)
{
	extract($args);

	$arr = xarMod::apiFunc('path','user','checkpath',array('path' => $path));
 
	if ($arr) {
		return reset($arr);
	} else {
		return false;
	}
}

?>

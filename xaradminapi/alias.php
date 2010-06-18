<?php
/**
 * path2alias
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
 * Manage aliases for a path...
  
$pathstart = the first part of the path

-- If there is no module alias for the pathstart, register one in the modules module and return true.
-- If the pathstart is a registered module alias, check if it's an alias for the same module specified in the action.  If it is not, return the name of the module for which pathstart is already an alias. 

 * @param $args['path'] required string the path
 * @param $args['actionmodule'] required string the module specified in the action
 */
function path_adminapi_alias($args) {

	extract($args);

	$path = substr($path, 1);
	$pos = strpos($path, '/');
	if($pos) {
		$pathstart = substr($path, 0, $pos);
	} else {
		$pathstart = $path;
	}


	$aliases = xarConfigVars::get(null, 'System.ModuleAliases');

	if (empty($aliases[$pathstart])) { 
		// There's no alias for this $pathstart, so register one...
		xarModAlias::set($pathstart, $actionmodule);
		return true;
	} else {
		// $pathstart is already registered as an alias
		$aliasmodule = $aliases[$pathstart];
		if($actionmodule != $aliasmodule) {
			// $pathstart is already an alias for a module other than the module in the action
			return array('pathstart' => $pathstart, 'aliasmodule' => $aliasmodule);
		} else {
			// $pathstart is already an alias for the module specified in the action
			return true;
		}
	}
}
?>

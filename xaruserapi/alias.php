<?php
/**
 * @package modules
 * @copyright (C) 2002-2009 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage content
 * @link http://www.xaraya.com/index.php/release/1015.html
 * @author potion <potion@xaraya.com>
 */
/**
 * Manage aliases for a path
  
$pathstart = the first part of the path

 * @param $args['path'] required string the path

 * If the pathstart is a module name, return the the module name.
 * If the pathstart isn't an alias, register it as an alias for the content module and return true.
 * If the pathstart is a registered module alias, check if it's an alias for the content module.  If it is, return true.  If it's not, return an array containing the pathstart and the name of the module for which it's an alias.

 */
function content_userapi_alias($args) {

    extract($args);

    $path = substr($path, 1);
    $pos = strpos($path, '/');
    if($pos) {
        $pathstart = substr($path, 0, $pos);
    } else {
        $pathstart = $path;
    }

    // check if the pathstart is a module name
    $modulesobject = DataObjectMaster::getObjectList(array('name' => 'modules'));
    $filters['where'] = 'name eq \'' . $pathstart . '\'';
    $modules = $modulesobject->getItems($filters);

    if (!empty($modules)) {
        $modulename = $pathstart;
        return $modulename; // pathstart is a module name
    }

    $aliases = xarConfigVars::get(null, 'System.ModuleAliases');
    if (!isset($aliases[$pathstart])) { 
        xarModAlias::set($pathstart, 'content');
        return true;
    } else {
        // $pathstart is already registered as an alias
        $aliasmodule = $aliases[$pathstart];
        if ($aliasmodule == 'content') {
            return true;
        } else {
            // $pathstart is already an alias for a module other than the module in the action
            return array('pathstart' => $pathstart, 'aliasmodule' => $aliasmodule);
        }
    }
}
?>

<?php
/**
 * Encode Short URLS
 *
 * @package modules
 * @copyright (C) 2002-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Content Module
 * @link http://www.xaraya.com/index.php/release/eid/1118
 * @author potion <ryan@webcommunicate.net>
 */
/**
 * return the path for a short URL to xarModURL for this module
 *
 * @author the Example module development team
 * @param $args the function and arguments passed to xarModURL
 * @returns string
 * @return path to be added to index.php for a short URL, or empty if failed
 */
function content_userapi_encode_shorturl($args)
{
    // Get arguments from argument array
    extract($args);
	/*
	xarModURL('content','user','display',array('itemid'=>1))
	xarModURL('content','user','view',array('name'=>'apples'))
	array(2) { ["name"]=> string(6) "apples" ["func"]=> string(4) "view" }
	*/

    // Check if we have something to work with
    if (!isset($func)) {
        return;
    }

	/*if (xarModVars::get('content','path_module')) {
		$args['module'] = 'content';
		$action = xarMod::apiFunc('path','admin','standardizeaction',array('action' => $args));
		$path = xarMod::apiFunc('path','user','action2path',array('action' => $action));
		if ($path) {
			return '/' . $path;
		}
	}*/

    // default path is empty -> no short URL
    $path = '';
    // if we want to add some common arguments as URL parameters below
    $join = '?';
    // we can't rely on xarModGetName() here -> you must specify the modname !
    $module = 'content';

	if (isset($args['ctype'])) {
		$ctype = $args['ctype'];
		$aliases = xarConfigVars::get(null, 'System.ModuleAliases');
		if(isset($aliases[$ctype]) && $aliases[$ctype] == 'content') {
			$module = $ctype;
		}
	}

    if ($func == 'main') {

        $path = '/' . $module . '/';

    } elseif ($func == 'view') {

		$path = '/' . $module;

		if ($module == 'content' && isset($ctype)) {
			$path .= '/view/' . $ctype;
		}

    } elseif ($func == 'display') {
  
		if(isset($args['itemid'])) {

			$object = DataObjectMaster::getObject(array('name' => 'content'));
			$object->getItem(array('itemid' => $itemid));
			$path = $object->properties['path']->value;
			if (!empty($path)) return $path;
			
			$path = '/' . $module;
			$path .= '/' . $itemid;

		} else { // no itemid
			return;
		}

    } else {
		// no main, view or display func
		return;
	}

    return $path;
}

?>
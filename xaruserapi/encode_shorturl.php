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

    // Check if we have something to work with
    if (!isset($func)) {
        return;
    }
 
    $path = '';
    $join = '?'; 
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

		$suppress = xarModVars::get('content','suppress_view_alias');
		$suppress = explode(',',$suppress);

		if (isset($ctype)) {
			if ($module == 'content') {
				$path .= '/view/' . $ctype;
			} elseif (in_array($ctype,$suppress)) { 
				$path .= '/view';
			}
		}

    } elseif ($func == 'display') {
  
		if (isset($args['itemid'])) {

			$object = DataObjectMaster::getObject(array('name' => 'content'));
			$object->getItem(array('itemid' => $itemid));
			$path = $object->properties['item_path']->value;
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
<?php
/**
 * Encode Short URLS
 *
 * @package modules
 * @copyright (C) 2002-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Downloads Module
 * @link http://www.xaraya.com/index.php/release/eid/1152
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
function downloads_userapi_encode_shorturl($args)
{
    // Get arguments from argument array
    extract($args);
	/*
	xarModURL('downloads','user','display',array('itemid'=>1))
	xarModURL('downloads','user','view',array('name'=>'apples'))
	array(2) { ["name"]=> string(6) "apples" ["func"]=> string(4) "view" }
	*/

    // Check if we have something to work with
    if (!isset($func)) {
        return;
    }

    // default path is empty -> no short URL
    $path = '';
    // if we want to add some common arguments as URL parameters below
    $join = '?';
    // we can't rely on xarModGetName() here -> you must specify the modname !
    $module = 'downloads';

    // specify some short URLs relevant to your module
    if ($func == 'main') {
        $path = '/' . $module . '/';

        // Note : if your main function calls some other function by default,
        // you should set the path to directly to that other function

    } elseif ($func == 'view') {

		$path = '/' . $module . '/view';

		// If we're not using an alias, we'll need the name after the func
		if ($module == 'downloads' && isset($ctype)) {
			$path .= '/' . $ctype;
		}

    } elseif ($func == 'display') {
 
		$path = '/' . $module . '/display';

		if(isset($args['itemid'])) {
			$path .= '/' . $itemid;
		} else { // no itemid
			return;
		}

	 } elseif ($func == 'getfile') {
 
		$path = '/' . $module;

		if(isset($args['itemid'])) {
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
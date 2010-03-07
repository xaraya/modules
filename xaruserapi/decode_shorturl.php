<?php
/**
 * Extract function and arguments from short URLS
 *
 * @package modules
 * @copyright (C) 2002-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage fedexws Module
 * @link http://www.xaraya.com/index.php/release/eid/1031
 * @author potion <ryan@webcommunicate.net>
 */
/**
 * extract function and arguments from short URLs for this module, and pass
 * them back to xarGetRequestInfo()
 *
 * @author the Example module development team
 * @param $params array containing the different elements of the virtual path
 * @returns array
 * @return array containing func the function to be called and args the query
 *         string arguments, or empty if it failed
 */
function fedexws_userapi_decode_shorturl($params)
{
    // Initialise the argument list we will return
    $args = array();

    // Analyse the different parts of the virtual path
    // $params[1] contains the first part after index.php/fedexws

    if (empty($params[1])) {
        // nothing specified -> we'll go to the main function
        return array('view', $args);

    } elseif (preg_match('/^index/i',$params[1])) {
        // some search engine/someone tried using index.html (or similar)
        // -> we'll go to the main function
        return array('main', $args);

    } elseif (preg_match('/^list$/i',$params[1])) {
        // something that starts with 'list' is probably for the view function
        // Note : make sure your encoding/decoding is consistent ! :-)
        return array('view', $args);

    } elseif (preg_match('/^(\w+)$/',$params[1], $matches)) {
        
		$args['name'] = $matches[0];
		$func = 'view';

		if (!empty($params[2]) && preg_match('/^(\d+)$/', $params[2], $matches)) {

			$args['itemid'] = $matches[0];
			$func = 'display';

		} else {

			foreach ($params as $key=>$param) {
				if ($key > 1) {
					$path_array[] = $param;
				}
			}

			$args['path'] = implode('/',$path_array);
			$func = 'display_path';

		}
		
        return array($func, $args);

    } else {

    }

    // default : return nothing -> no short URL decoded
}

?>
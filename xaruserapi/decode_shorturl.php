<?php
/**
 * Extract function and arguments from short URLS
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
 * extract function and arguments from short URLs for this module, and pass
 * them back to xarGetRequestInfo()
 *
 * @param $params array containing the different elements of the virtual path
 * @returns array
 * @return array containing func the function to be called and args the query
 *         string arguments, or empty if it failed
 */
function downloads_userapi_decode_shorturl($params)
{

	$func = 'main';

    // Initialise the argument list we will return
    $args = array();

	if ($params[0] != 'downloads') {
		$alias = true;
	} else {
		$alias = false;
	}

	if (is_numeric($params[1])) {
		$func = 'getfile';
		$args['itemid'] = $params[1];
	}

	return array($func, $args);  

}

?>
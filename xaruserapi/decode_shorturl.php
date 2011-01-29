<?php
/**
 * Extract function and arguments from short URLS
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
 * extract function and arguments from short URLs for this module, and pass
 * them back to xarGetRequestInfo()
 *
 * @param $params array containing the different elements of the virtual path
 * @returns array
 * @return array containing func the function to be called and args the query
 *         string arguments, or empty if it failed
 */
function content_userapi_decode_shorturl($params)
{ 
    // Initialise the argument list we will return
    $args = array();

	$qs = '';

	if (isset($_GET)) {
		$join = '';
		foreach($_GET as $key => $val) { 
			$qs .= $join . $key;
			if (!empty($val) || $val == 0) {
				$qs .= "=" . $val; 
			}
			$join = '&';
		}  
	}

	if ($params[0] == 'content') {
		if (!isset($params[1])) {
			$args = array();
			if ($_GET) $args = $_GET;
			return array('view', $args);
		} 
		if (is_numeric($params[1])) { 
			$args['itemid'] = $params[1];
			if ($_GET) array_merge($args, $_GET);
			return array('display', $args);
		}
		if ($params[1] == 'display') { 
			if (is_numeric($params[2])) { 
				$args['itemid'] = $params[2];
				if ($_GET) array_merge($args, $_GET);
				return array('display', $args);
			} 
		}
		if ($params[1] == 'view' && isset($params[2])) { 
			$args['ctype'] = $params[2];
			return array('view', $args); 
		}
		$checkpath = false;

	} else {
		// it's an alias

		if (isset($params[1])) {
			if (is_numeric($params[1])) {
				$args['itemid'] = $params[1];
				if ($_GET) array_merge($args, $_GET);
				return array('display', $args);
			}
			if ($params[1] == 'view') {
				$args['ctype'] = $params[0];  
				if ($_GET) array_merge($args, $_GET);
				return array('view', $args);
			}
		}

		if (!isset($params[1])) {
			$args['ctype'] = $params[0]; 
			//tell the view func to check if the alias is a ctype
			$args['check_ctype'] = true; 
			if ($_GET) array_merge($args, $_GET);
			return array('view', $args);
		}
		
		// no view functions allowed down here...

		$baseurl = xarServer::GetBaseURL();
		$url = xarServer::GetCurrentURL();
		$path = str_replace($baseurl, '/', $url);
		$path = str_replace('/index.php', '', $path);
		
		// checking the path in here means we aren't going to allow /content/foo/etc
		$checkpath = xarMod::apiFunc('content','user','checkpath',array('path' => $path)); 

		if ($checkpath) {
			if(!empty($qs)) {
				array_merge($args, $_GET);
			}
			$args['itemid'] = $checkpath;
			return array('display',$args);
		} else {
			$msg = 'You\'re seeing this message because the resource <span class="path_not_found">' . $path . '</span> does not exist in the content module.';
			return array('notfound',array('msg' => $msg));
		}
	} 

}

?>
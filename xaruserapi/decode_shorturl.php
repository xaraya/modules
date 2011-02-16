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
function content_userapi_decode_shorturl($params) { 

    // Initialise the argument list we will return
    $args = array();

	/*
	$qs = '';

	if (isset($_GET)) { 
		$join = '';
		foreach($_GET as $key => $val) { 
			$qs .= $join . $key;
			if (!empty($val) || $val === '0') {
				$qs .= "=" . $val; 
			}
			$join = '&';
		}  
	} 
	*/ 

	if ($params[0] == 'content') {
		if (!isset($params[1])) {
			$args = array();
			if ($_GET) $args = $_GET;
			return array('main', $args);
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
		if ($params[1] == 'view') {  
			if (isset($params[2])) $args['ctype'] = $params[2];
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
			// add 'view' to the end of URLs to avoid the processing & db hits below
			if ($params[1] == 'view' && !isset($params[2])) {
				$args['ctype'] = $params[0];  
				if ($_GET) array_merge($args, $_GET);
				return array('view', $args);
			}
		} else {
			// we have a 1-param URL.  If the param is a ctype, call the view function.
				$suppress = xarModVars::get('content','suppress_view_alias');
				$alias = $params[0];
				if (!isset($suppress[$alias])) {
					$data['content_types'] = xarMod::apiFunc('content','admin','getcontenttypes');
					if (isset($data['content_types'][$alias])) {
						$args['ctype'] = $params[0];   
						if ($_GET) array_merge($args, $_GET);
						return array('view', $args);
					}
				}
		}
		
		 /*  We have ruled out these URLs:
			 /<alias>/123  (display func)
			 /<alias>/view (view func)
			 /<alias> (view func if alias is a ctype) 
		*/

		$baseurl = xarServer::GetBaseURL();
		$url = xarServer::GetCurrentURL();
		$path = str_replace($baseurl, '/', $url); 
		$path = str_replace('/index.php', '', $path);
		$url = parse_url($path); //it works even after we remove baseurl + /index.php
		$path = $url['path'];  
		
		/*$path = str_replace('?'.$qs, '', $path);  */

		// checking the path in here means we aren't going to allow /content/foo/etc
		$checkpath = xarMod::apiFunc('content','user','checkpath',array('path' => $path)); 
 
		if ($checkpath) { 

			if(isset($_GET)) {
				array_merge($args, $_GET);
			}
			$args['itemid'] = $checkpath;
			return array('display',$args);

		} elseif (!isset($params[1])) {
			// If we're here, we had to do both a ctype lookup and a path lookup.  
			if(isset($_GET)) {
				array_merge($args, $_GET);
			}
			return array('main',$args);
		} else {

			$notfound = 'You\'re seeing this message because the resource <span class="path_not_found">' . $path . '</span> does not exist in the content module.';
			return array('notfound',array('msg' => $notfound));

		}
	} 

}

?>
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

	$ctalias = false;

	if ($params[0] == 'content') {
		if (!isset($params[1]) {
			//always 
			return array('view', array());
		} 
		if (is_numeric($params[1])) { 
			return array('display', array('itemid' => $params[1]));
		}
		if ($params[1] == 'display') { 
			if (is_numeric($params[2])) { 
				return array('display', array('itemid' => $params[2]));
			} 
		}
		$checkpath = false;
	} else {
		// check if the alias is a ctype
		$content_types = xarMod::apiFunc('content','admin','getcontenttypes');
		if (isset($content_types[$params[0]])) {
			if (!isset($params[1] || $params[1] == 'view') {
				//always
				return array('view', array('name' => $params[0]));
			}
			//it still could be a display function
		} 

		if (!isset($params[1]) {
			//must be a module alias but not a content type
			return array('view', array());
		}

		if (is_numeric($params[1])) {
			//always
			return array('display', array('itemid' => $params[1]));
		}

		// no longer any chance it's a view function...

		$baseurl = xarServer::GetBaseURL();
		$url = xarServer::GetCurrentURL();
		$path = str_replace($baseurl, '/', $url);
		$path = str_replace('/index.php', '', $path);

		if (isset($_GET)) {
			$qs = '';
			$join = '';
			foreach($_GET as $key => $val) { 
				$qs .= $join . $key;
				if (!empty($val) || $val == 0) {
					$qs .= "=" . $val; 
				}
				$join = '&amp;';
			}  
			$path = str_replace('?'.$qs,'',urldecode($path));
		}
		// checking the path in here means we aren't going to allow /content/foo/etc
		$checkpath = xarMod::apiFunc('content','user','checkpath',array('path' => $path)); 
	} 

	/*if (xarModVars::get('content','path_module')) {
		$action = xarMod::apiFunc('path','user','path2action',array('path' => $path)); 
	} else {
		$action = false;
	} */

	if ($checkpath) {
		if(isset($q)) {
			array_merge($args, $_GET);
		}
		$args['itemid'] = $checkpath;
		return array('display',$args);
	} else {
		$msg = 'You\'re seeing this message because the itemid ' . $itemid . ' does not exist in the content module.';
		return xarTplModule('base','message','notfound',array('msg' => $msg));
	}

}

?>
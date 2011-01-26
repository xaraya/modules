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

	$func = 'main';

    // Initialise the argument list we will return
    $args = array();

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

	$ctalias = false;

	if ($params[0] != 'content') {
		$alias = true;
		// check if the alias is a ctype
		$content_types = xarMod::apiFunc('content','admin','getcontenttypes');
		if (isset($content_types[$params[0]])) {
			$ctalias = true;
		} 
	} else {
		$alias = false;
	}

	if (xarModVars::get('content','path_module')) {
		$action = xarMod::apiFunc('path','user','path2action',array('path' => $path)); 
	} else {
		$action = false;
	} 

	if($action) {
		$action = unserialize($action);
		foreach ($action as $key=>$value) {
			$args[$key] = $action[$key];
		} 

		if (isset($args['func'])) {
			$func = $args['func'];
		}

		if (isset($args['itemid']) && !isset($args['func'])) {
			$func = 'display';
		}

	} elseif ($ctalias) {

		$args['ctype'] = $params[0];

		if (isset($params[1])) {
			if (is_numeric($params[1])) {
				$func = 'display';
				$args['itemid'] = $params[1];
			} else {
				$func = $params[1];
				if ($func == 'display') {
					if (isset($params[2])) {
						$args['itemid'] = $params[2];
					} else {
						return;
					}
				}
			}
		} else {
			$func = 'view';
		}

	} else {
 
		if (isset($params[1])) {
			if (is_numeric($params[1])) {
				$func = 'display';
				$args['itemid'] = $params[1];
			} else {
				$func = $params[1];
				if ($func == 'display') {
					if (isset($params[2])) {
						$args['itemid'] = $params[2];
					} else {
						return;
					}
				} elseif ($func == 'view') {
					if (isset($params[2])) {
						$args['ctype'] = $params[2];
					}
				}
			}
		} else {
			$func = xarModVars::get('modules', 'defaultmodulefunction');
		}

	}

	if(isset($q)) {
		array_merge($args, $_GET);
	}

	return array($func, $args);  

}

?>
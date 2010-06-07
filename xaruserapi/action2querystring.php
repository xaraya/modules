<?php
/**
 * Format an action as a query string
 *
 * @package modules
 * @copyright (C) 2002-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Path Module
 * @link http://www.xaraya.com/index.php/release/eid/1150
 * @author potion <ryan@webcommunicate.net>
 */
/**
 *  Format an action as a query string.  The resulting query string will have modules, type and func as the first three keys, with the rest of the arguments arranged alphabetically by key. (Ex.: module=content&type=user&func=view&axe=yes&bus=yellow&zebra=striped)

 * @param args[$action] required action (must be an associative array and can be serialized or not)
 */
function path_userapi_action2querystring($args)
{

    if (!xarSecurityCheck('ViewPath')) return;

	extract($args);

	if (!is_array($action)) {
		$action = unserialize($action);
	}

	$qs = array();
 
	$arr['module'] = $action['module'];
	$arr['type'] = $action['type'];
	$arr['func'] = $action['func'];

	unset($action['module']);
	unset($action['type']);
	unset($action['func']);

	if(!empty($action)) {
		ksort($action);
		$arr = array_merge($arr, $action);
	}

	foreach($arr as $key=>$value) {
		$qs[] = $key . '=' . $value;
	}

	$qs = implode('&', $qs);

	return $qs;

}

?>

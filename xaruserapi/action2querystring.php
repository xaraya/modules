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
 *  format an action as a query string
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

	foreach ($action as $key=>$value) {
		$qs[] = $key . '=' . $value;
	}

	$qs = implode('&', $qs);

	return $qs;

}

?>

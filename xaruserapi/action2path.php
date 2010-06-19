<?php
/**
* Return the path for an action
*
 * @package modules
 * @copyright (C) 2002-2009 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage content
 * @link http://www.xaraya.com/index.php/release/eid/1150
 * @author potion <potion@xaraya.com>
 */
/**
* Return the path for an action, or return false if there is no path for the action
 * @param $args['action'] required array action
 */
function path_userapi_action2path($args) {

	extract($args);

	$arr = xarMod::apiFunc('path','user','checkaction',array('action' => $action));

	if ($arr) {
		return reset($arr);
	} else {
		return false;
	}

} 
?>
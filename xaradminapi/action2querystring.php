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
function path_adminapi_action2querystring($args)
{

	extract($args);

	$arr = xarMod::apiFunc('path','admin','standardizeaction',array('action' => $action));

	$qs = http_build_query($arr, NULL, '&');

	return $qs;

}

?>

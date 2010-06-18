<?php
/**
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
 *    Given a module and an itemid, return a URL
 *		This assumes all items created by the module have a unique itemid
  * @param args[$module] required string module
  * @param args[$itemid] required itemid
  * @param args[$display] optional specify a different function name for the xarModURL fallback
  * @param args[$id] optional specify a different itemid field for the xarModURL fallback
 */
function path_userapi_itemurl($args){

	$path = xarMod::apiFunc('path','user','action2path',array('action' => $args));

	if (!$path) { // Try to fall back on xarModURL
		$id = 'itemid'; 
		$display = 'display';
		extract($args);
		return xarModURL($module,'user',$display,array($id=>$itemid));
	}
	//$BaseModURL = xarCore::getSystemVar('BaseModURL', true);
	if (!isset($BaseModURL)) {
		$BaseModURL = 'index.php';
	}

	return xarServer::GetBaseURL() . $BaseModURL . $path;
	
}
?>
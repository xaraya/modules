<?php

/**
 * File: $Id$
 *
 * Create a repository
 *
 * @package modules
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @link http://www.xaraya.com
 * 
 * @subpackage bkview
 * @author Marcel van der Boom <marcel@xaraya.com>
*/

/**
 * create a new bkview item
 */
function bkview_adminapi_create($args)
{
	extract($args);
	$invalid = array();
	if (!isset($reponame) || !is_string($reponame)) $invalid[] = 'reponame';
	if (!isset($repopath) || !is_string($repopath)) $invalid[] = 'repopath';
	if (count($invalid) > 0) {
		$msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                     join(', ',$invalid), 'admin', 'create', 'bkview');
		xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
		return;
	}
	
	if (!xarSecurityCheck('AdminAllRepositories')) return;
	
	$dbconn =& xarDBGetConn();
	$xartable =& xarDBGetTables();
	$bkviewtable = $xartable['bkview'];
	$nextId = $dbconn->GenId($bkviewtable);
	
	$sql = "INSERT INTO $bkviewtable (
              xar_repoid,
              xar_name,
              xar_path)
            VALUES (
              $nextId,
              '" . xarVarPrepForStore($reponame) . "',
              '" . xarvarPrepForStore($repopath) . "')";
	if(!$dbconn->Execute($sql)) return;
	
	$repoid = $dbconn->PO_Insert_ID($bkviewtable, 'xar_repoid');
	
	$item = $args;
	$item['module'] = 'bkview';
	$item['itemid'] = $repoid;
	xarModCallHooks('item', 'create', $repoid, $item);
	return $repoid;
}
?>
<?php


/**
 * File: $Id$
 *
 * Short description of purpose of file
 *
 * @package modules
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @link http://www.xaraya.com
 * 
 * @subpackage bkview
 * @author Marcel van der Boom <marcel@xaraya.com>
*/

/**
 * delete a bkview item
 *
 * @author the Bkview module development team
 * @param $args['exid'] ID of the item
 * @returns bool
 * @return true on success, false on failure
 * @raise BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
 */
function bkview_adminapi_delete($args)
{
	extract($args);
	
	if (!isset($repoid) || !is_numeric($repoid)) {
		$msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                     'item ID', 'admin', 'delete', 'Bkview');
		xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
		return;
	}
    
	$item = xarModAPIFunc('bkview',	'user','get',array('repoid' => $repoid));
	
	// Check for exceptions
	if (!isset($item) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; // throw back
	
	if (!xarSecurityCheck('AdminAllRepositories')) return;
	
	$dbconn =& xarDBGetConn();
	$xartable =& xarDBGetTables();
	
	$bkviewtable = $xartable['bkview'];

	$sql = "DELETE FROM $bkviewtable
            WHERE xar_repoid = " . xarVarPrepForStore($repoid);
	if(!$dbconn->Execute($sql)) return;

	// Let any hooks know that we have deleted an item.  As this is a
	// delete hook we're not passing any extra info
	//    xarModCallHooks('item', 'delete', $exid, '');
	$item['module'] = 'bkview';
	$item['itemid'] = $repoid;
	xarModCallHooks('item', 'delete', $repoid, $item);
	
	// Let the calling process know that we have finished successfully
	return true;
}
?>
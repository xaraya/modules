<?php
/**
 * Photoshare by Chris van de Steeg
 * based on Jorn Lind-Nielsen 's photoshare
 * module for PostNuke
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2002 by the Xaraya Development Team.
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.com
 * @subpackage photoshare
 * @author Chris van de Steeg
 */
 
function photoshare_userapi_getuserinfo($args)
{
	$uid = xarUserGetVar('uid');
	extract($args);
    
	$dbconn =& xarDBGetConn();
	$xartables =& xarDBGetTables();
	
	$imagesTable  = $xartables['photoshare_images'];
	
	// FIXME: what if not logged in - how about 'owner' ???
	
	$sql = "SELECT 	SUM(ps_bytesize)
	      	FROM 	$imagesTable 
	      	WHERE	ps_owner = " . xarVarPrepForStore($uid);
	
	$result =& $dbconn->execute($sql);
	
	if (!$result) return; 
	
	$userSetup = xarModAPIFunc('photoshare', 'user', 'getusersetup', array( 'uid' => $uid ));
	
	return array( 'totalCapacityUsed'  => $result->fields[0],
	            'imageSizeLimitSingle' => xarModGetVar('photoshare', 'imageSizeLimitSingle'),
	            'imageSizeLimitTotal'  => $userSetup['storage'] );
	
}
 
?>

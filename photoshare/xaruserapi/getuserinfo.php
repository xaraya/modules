<?php
/**
 * Photoshare by Jorn Lind-Nielsen (C) 2002.
 *
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.com
 * @subpackage photoshare
 * @author Jorn Lind-Nielsen / Chris van de Steeg
 */
 
function photoshare_userapi_getuserinfo($args)
{
	$uid = xarUserGetVar('uid');
	extract($args);
    
	list($dbconn) = xarDBGetConn();
	$xartables = xarDBGetTables();
	
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
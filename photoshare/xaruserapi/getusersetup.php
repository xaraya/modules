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
 
function photoshare_userapi_getusersetup($args)
{
	$uid = xarUserGetVar('uid');
	extract($args);
	if (!isset($inrecursion))
		$inrecursion = false;
	
	// Argument check
	if (!isset($uid)) {
        $msg = xarML('Bad param #(1) for #(2) function #(3)() in module #(4)',
            'uid', 'userapi', 'getusersetup', 'Photoshare');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return;
	}
		
	$dbconn =& xarDBGetConn();
	$xartables =& xarDBGetTables();
	
	$setupTable   = $xartables['photoshare_setup'];
	
	$sql = "SELECT MAX(ps_storage)
	     	FROM   $setupTable
		    WHERE  ps_id = " . xarVarPrepForStore($uid);

	$result =& $dbconn->execute($sql);
    if (!$result) return; 
	
	if ($result->EOF) {
		$roles = new xarRoles();
		$role = $roles->getRole($uid);
		//$role = new xarRole();
		$parents = $role->getParents();
		if (!$inrecursion && (!isset($parents) || count($parents) == 0)) {
			$storage = xarModGetVar('photoshare', 'imageSizeLimitTotal');
		} else {
			for ($i=0;$i<count($parents);$i++) {
				$parent = $parents[$i];
				$setup =  photoshare_userapi_getusersetup(array('uid' => $parent->getID(), 'inrecursion'=>true));
				if (!isset($setup)) return;
				if (array_key_exists('storage', $setup)) {
					$storage = $setup['storage'];
					break;
				}
			}
		}
	}
	if (!isset($storage) && !$inrecursion) {
		$storage = xarModGetVar('photoshare', 'imageSizeLimitTotal');
	} else {
		return array();
	}
	
	return array( 'storage' => $storage );
}
 
?>

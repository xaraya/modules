<?php
/**
 * File: $Id: s.xarinit.php 1.11 03/01/18 11:39:31-05:00 John.Cox@mcnabb. $
 * 
 * Xaraya Referers
 * 
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2002 by the Xaraya Development Team.
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.org
 * @subpackage Referer Module
 * @author John Cox et al. 
 */
function referer_adminapi_delete_one($args)
{ 
    extract($args);
    // Security Check
    if (!xarSecurityCheck('DeleteReferer')) return; 
    if (!isset($rid)) {
        $msg = xarML('Invalid Parameter Count');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return;
    }
    // Get database setup
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $referertable = $xartable['referer']; 
    // Delete the item
    $query = "DELETE FROM $referertable
              WHERE xar_rid = ?";
    $bindvars = array($rid);
    $result =& $dbconn->Execute($query,$bindvars);
    // Check for an error
    if (!$result) return; 
    // Let the calling process know that we have finished successfully
    return true;
} 
?>
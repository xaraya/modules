<?php
/**
 * File: $Id:
 * 
 * Update state of a Bible text
 * 
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage bible
 * @author curtisdf 
 */
/**
 * update state of a Bible text
 * 
 * @author curtisdf 
 * @param  $args ['tid'] text ID to apply change to
 * @param  $args ['newstate'] new state to change to
 * @returns bool
 * @return true on success, false on failure
 * @raise BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
 */
function bible_adminapi_setstate($args)
{

    extract($args);

    $invalid = array();
    if (!isset($tid) || !is_numeric($tid)) {
        $invalid[] = 'tid';
    } 
    if (!isset($newstate) || !is_numeric($newstate)) {
        $invalid[] = 'newstate';
    } 
    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
            join(', ', $invalid), 'admin', 'setstate', 'Bible');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
            new SystemException($msg));
        return;
    } 

    // need to get short name of this text before we do security check
    $text = xarModAPIFunc('bible', 'user', 'get', array('tid' => $tid));

    if (!xarSecurityCheck('EditBible', 1, 'Text', "$text[sname]:$text[tid]")) {
        return;
    } 

    $dbconn = xarDBGetConn();
    $xartable = xarDBGetTables(); 

    $texttable = $xartable['bible_texts']; 

    $query = "UPDATE $texttable
                SET xar_state = ?
                WHERE xar_tid = ?";

    $bindvars = array($newstate, $tid);
    $result = $dbconn->Execute($query,$bindvars);
    
    if (!$result) return; 

    return true;
} 

?>

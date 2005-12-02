<?php
/**
 * File: $Id:
 * 
 * Delete a text
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
 * delete a text
 * 
 * @author curtisdf 
 * @param  $args ['tid'] ID of the text
 * @returns bool
 * @return true on success, false on failure
 * @raise BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
 */
function bible_adminapi_delete($args)
{ 
    extract($args); 

    if (!isset($tid) || !is_numeric($tid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
            'item ID', 'admin', 'delete', 'Bible');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
            new SystemException($msg));
        return;
    } 

    $text = xarModAPIFunc('bible', 'user', 'get',
                          array('tid' => $tid)); 

    // Check for exceptions
    if (!isset($text) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; // throw back

    if (!xarSecurityCheck('DeleteBible', 1, 'Text', "$text[sname]:$tid")) {
        return;
    } 

    // delete the table for this text, if it exists
    list($textdbconn,
         $texttable) = xarModAPIFunc('bible', 'user', 'getdbconn', array('tid' => $tid));

    $query = "DROP TABLE IF EXISTS $texttable";
    $textdbconn->Execute($query);
    if ($textdbconn->ErrorNo()) return; 

    // delete record from texts table
    $textsdbconn = xarDBGetConn();
    $xartable = xarDBGetTables(); 
    $textstable = $xartable['bible_texts'];

    $query = "DELETE FROM $textstable WHERE xar_tid = ?";
    $textsdbconn->Execute($query,array($tid)); 
    if ($textsdbconn->ErrorNo()) return; 

    // Let the calling process know that we have finished successfully
    return true;
} 

?>

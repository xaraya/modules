<?php
/**
 * File: $Id:
 *
 * Count the words in a dictionary
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
 * Count the words in a dictionary
 *
 * @author curtisdf
 * @returns integer
 * @return number of texts held by this module
 * @raise DATABASE_ERROR
 */
function bible_userapi_countwords($args)
{
    // security check
    if (!xarSecurityCheck('ViewBible')) return;

    extract($args);

    $invalid = array();
    if (!empty($sname) && !is_string($sname)) {
        $invalid[] = 'sname';
    }
    if (isset($tid) && !is_numeric($tid)) {
        $invalid[] = 'tid';
    }
    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
            join(', ', $invalid), 'user', 'countwords', 'Bible');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
            new SystemException($msg));
        return;
    }

    // get tid
    if (empty($tid)) {
        $text = xarModAPIFunc('bible', 'user', 'get', array('sname' => $sname));
        $tid = $text['tid'];
    }

    // prepare for database
    $dbconn = xarDBGetConn();
    $xartable = xarDBGetTables();
    $texttable = $xartable['bible_text'] . "_$tid";

    // generate query
    $query = "SELECT COUNT(1) FROM $texttable WHERE 1";
    $bindvars = array();

    // execute query
    $result = $dbconn->Execute($query, $bindvars);
    if (!$result) return;

    // retrieve the number and close db connection
    list($numitems) = $result->fields;
    $result->Close();

    return $numitems;
}

?>

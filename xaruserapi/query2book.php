<?php
/**
 * File: $Id:
 * 
 * Translate a query to a book name
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
 * translate a query to a book name
 * 
 * @author curtisdf
 * @param  $args ['query'] query to extract book from
 * @param  $args ['sname'] (optional) short name of text to compare to
 * @returns array
 * @return array of alias data, or false if no match
 * @raise BAD_PARAM, DATABASE_ERROR, NO_PERMISSION
 */
function bible_userapi_query2book($args)
{
    extract($args);

    // Argument check
    $invalid = array();
    if (!isset($query)) {
        $invalid[] = 'query';
    }
    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
            join(', ', $invalid), 'user', 'query2book', 'Bible');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
            new SystemException($msg));
        return;
    }

    // security check
    if (!xarSecurityCheck('ViewBible')) return;

    // get the part of the query that might be a Bible book
    $book = preg_replace("/( \d{1,3}(\:\d{1,3}\w?(\-\d{1,3}\w?)?)?)?(\s+para(llel)?([^ \$]+))?\$/", '', $query);
#    $book = xarVarPrepForStore($book);

    $dbconn = xarDBGetConn();
    $xartable = xarDBGetTables(); 

    $aliastable = $xartable['bible_aliases']; 
    $sqlquery = "SELECT xar_sword, xar_display
                 FROM $aliastable
                 WHERE (
                 xar_aliases REGEXP ? OR
                 xar_aliases REGEXP ? OR
                 xar_aliases REGEXP ? OR
                 xar_aliases LIKE ? OR
                 xar_display LIKE ?
                 )";
    $bindvars = array(",$book,", "^$book,", ",$book$", "$book", "$book");
    $result = $dbconn->Execute($sqlquery, $bindvars);

    if (!$result) return; 
    if ($result->EOF) return;

    list($sword, $display) = $result->fields;
    $result->Close(); 

    // if sname was given, make sure book actually exists in text
    if (isset($sname)) {

        $text = xarModAPIFunc('bible', 'user', 'get', array('sname' => $sname));

        list($textdbconn,
            $texttable) = xarModAPIFunc('bible', 'user', 'getdbconn',
                                        array('tid' => $text['tid']));

        $query = "SELECT DISTINCT xar_book
                FROM $texttable
                WHERE xar_book LIKE ?";
        $bindvars = array($sword);

        $result = $textdbconn->Execute($query, $bindvars);

        if (!$result) return; 
        if ($result->EOF) return;
    }

    // return the sword name and the display name
    return array($sword, $display);

} 

?>

<?php
/**
 * File: $Id:
 * 
 * Determine the type of query requested
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
 * get query type
 * 
 * @author curtisdf 
 * @param sname $ (optional) short name of the text
 * @param tid $ text ID to look in
 * @param query $ the query to analyze
 * @returns string
 * @return name of query function ('lookup' or 'search'), or false on failure
 * @raise BAD_PARAM, DATABASE_ERROR, NO_PERMISSION
 */
function bible_userapi_getquerytype($args)
{ 
    extract($args);

    // Optional arguments
    if (!isset($query)) $query = '';

    // Argument check
    $invalid = array();
    if (!isset($tid) && !isset($sname)) $invalid[] = 'book identifier';
    if (isset($tid) && !is_numeric($tid)) $invalid[] = 'tid';
    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
            join(', ', $invalid), 'user', 'getquerytype', 'Bible');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
            new SystemException($msg));
        return;
    } 

    // security check
    if (!xarSecurityCheck('ViewBible')) return; 

    // get text ID if we don't have it
    if (!isset($tid)) {
        // translate short name into text id
        $text = xarModAPIFunc('bible', 'user', 'get',
                            array('sname' => $sname));
        $tid = $text['tid'];
    }

    // polish up the query
    $query = trim($query);

    // analyze query itself for clues to the search type
    if (stristr($query, '*') ||
        stristr($query, '"') ||
        preg_match("/^[\+\-]\w+/", $query)) {
        $function = 'view';
    } else {

        // check if query book matches an alias or display name
        $book = xarModAPIFunc('bible', 'user', 'query2book', array('query' => $query));

        if (empty($query)) {
            // no query, so it's a lookup (display TOC)
            $function = 'display';
        } elseif (empty($book)) {
            // book name not found in query, so it's a search
            $function = 'view';
        } else {
            // book name was found; it's a lookup
            $function = 'display';
        }
    }

    return $function;
} 

?>

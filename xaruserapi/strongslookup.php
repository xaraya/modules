<?php
/**
 * File: $Id:
 * 
 * Perform a Strong's Concordance lookup
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
 * strong's lookup
 * 
 * @author curtisdf 
 * @param  $args ['sname'] short name of text to look in
 * @param  $args ['tid'] text ID to look in
 * @param  $args ['objectid'] a generic object id (if called by other modules)
 * @param  $args ['query'] (optional) reference to look up
 * @returns array
 * @return result array, or false on failure
 * @raise BAD_PARAM, DATABASE_ERROR, NO_PERMISSION
 */
function bible_userapi_strongslookup($args)
{
    extract($args);

    // Optional args
    if (empty($query)) {
        $query = '';
    }
    if (!isset($rand)) {
        $rand = false;
    }

    // Argument check
    $invalid = array();
    if (!isset($sname) && !isset($tid)) {
        $invalid[] = 'text identifier';
    }
    if (isset($tid) && !is_numeric($tid)) {
        $invalid[] = 'tid';
    }
    if (isset($objectid) && !is_numeric($objectid)) {
        $invalid[] = 'objectid';
    }
    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
            join(', ', $invalid), 'user', 'strongslookup', 'Bible');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
            new SystemException($msg));
        return;
    }

    if (!empty($objectid)) {
        $tid = $objectid;
    }

    // get text data
    $args = array();
    if (isset($tid)) $args['tid'] = $tid;
    if (isset($sname)) $args['sname'] = $sname;
    $text = xarModAPIFunc('bible', 'user', 'get', $args);
    if (!isset($text) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; // throw back

    // extract text ID if we don't yet have it
    if (!isset($tid)) $tid = $text['tid'];

    // security check
    if (!xarSecurityCheck('ViewBible', 1, 'Text', "$text[sname]:$tid")) {
        return;
    } 

    // get database parameters
    list($textdbconn,
         $texttable) = xarModAPIFunc('bible', 'user', 'getdbconn',
                                     array('tid' => $tid));

    // if query was empty, give table of contents
    if (empty($query)) {
        $ref = xarML('Table of Contents');

        $results = array('ref' => $ref,
                         'type' => 'toc',
                         'text' => $text);

        return $results;
    }

    // prepare SQL parameters
    $sqlquery = "SELECT *
                 FROM $texttable
                 WHERE
                 (xar_num REGEXP ? OR
                  xar_word = ? OR
                  xar_pron = ?)";
    $bindvars = array('0{0,}'.addslashes($query), $query, $query);

    // get matches
    $result = $textdbconn->SelectLimit($sqlquery, 1, 0, $bindvars);
    if (!$result) return; 
    if ($result->EOF) return;

    list($wid, $num, $word, $pron, $def) = $result->fields;
    $result->Close();

    $entry = array('wid' => $wid,
                   'num' => $num,
                   'word' => $word,
                   'pron' => $pron,
                   'def' => $def);


    // assemble result parameters
    $results = array('ref' => $word,
                     'query' => $query,
                     'text' => $text,
                     'entry' => $entry);

    return $results;

} 

?>

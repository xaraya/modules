<?php
/**
* Get all issues
*
* @package unassigned
* @copyright (C) 2002-2005 by The Digital Development Foundation
* @license GPL {@link http://www.gnu.org/licenses/gpl.html}
* @link http://www.xaraya.com
*
* @subpackage ebulletin
* @link http://xaraya.com/index.php/release/557.html
* @author Curtis Farnham <curtis@farnham.com>
*/
/**
 * get all ebulletin items
 *
 * @author the eBulletin module development team
 * @param numitems $ the number of items to retrieve (default -1 = all)
 * @param startnum $ start with this item number (default 1)
 * @returns array
 * @return array of items, or false on failure
 * @raise BAD_PARAM, DATABASE_ERROR, NO_PERMISSION
 */
function ebulletin_userapi_getallissues($args)
{
    // security check
    if (!xarSecurityCheck('VieweBulletin')) return;

    extract($args);

    // set defaults
    if (empty($startnum)) $startnum = 1;
    if (empty($numitems)) $numitems = -1;
    if (empty($order)) $order = 'issuedate';
    if (empty($sort)) $sort = 'DESC';

    // validate vars
    $invalid = array();
    if (empty($startnum) || !is_numeric($startnum)) {
        $invalid[] = 'startnum';
    }
    if (empty($numitems) || !is_numeric($numitems)) {
        $invalid[] = 'numitems';
    }
    if (isset($order) && !in_array($order, array('id', 'pid', 'issuedate', 'name'))) {
        $invalid[] = 'order';
    }
    if (isset($sort) && ($sort != 'ASC' && $sort != 'DESC')) {
        $invalid[] = 'sort';
    }
    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
            join(', ', $invalid), 'userapi', 'getall', 'eBulletin');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return;
    }

    // prepare for database
    $dbconn = xarDBGetConn();
    $xartable = xarDBGetTables();
    $pubstable = $xartable['ebulletin'];
    $issuestable = $xartable['ebulletin_issues'];

    // generate query
    $query = "
        SELECT
            $issuestable.xar_id,
            $issuestable.xar_pid,
            $issuestable.xar_issuedate,
            $issuestable.xar_subject,
            $issuestable.xar_published,
            $pubstable.xar_name AS pubname
        FROM $issuestable, $pubstable
        WHERE $pubstable.xar_id = $issuestable.xar_pid
        ORDER BY $issuestable.xar_$order $sort
    ";

    // perform query
    $result = $dbconn->SelectLimit($query, $numitems, $startnum-1);
    if (!$result) return;

    // assemble results
    $issues = array();
    for (; !$result->EOF; $result->MoveNext()) {

        // extract this row
        list($id, $pid, $issuedate, $subject, $published, $pubname) = $result->fields;

        // build issue array
        if (xarSecurityCheck('VieweBulletin', 0, 'Publication', "$pubname:$pid")) {
            $issues[] = array(
                'id' => $id,
                'pid' => $pid,
                'issuedate' => $issuedate,
                'subject' => $subject,
                'published' => $published,
                'pubname' => $pubname
            );
        }
    }
    $result->Close();

    // success
    return $issues;
}

?>
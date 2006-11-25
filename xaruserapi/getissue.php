<?php
/**
* Get an issue
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
 * get a specific issue
 *
 * @author the eBulletin module development team
 * @param  $args ['id'] id of ebulletin item to get
 * @return array item array, or false on failure
 * @throws BAD_PARAM, DATABASE_ERROR, NO_PERMISSION
 */
function ebulletin_userapi_getissue($args)
{
    extract($args);

    // validate inputs
    if (!isset($id) || !is_numeric($id)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
            'issue ID', 'userapi', 'getissue', 'eBulletin');
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
            $issuestable.xar_body_html,
            $issuestable.xar_body_txt,
            $issuestable.xar_published,
            $pubstable.xar_name AS pubname
        FROM $issuestable, $pubstable
        WHERE $pubstable.xar_id = $issuestable.xar_pid
        AND $issuestable.xar_id = ?
    ";

    // perform query
    $result = $dbconn->Execute($query, array($id));
    if (!$result) return;

    // extract this row
    list(
        $id, $pid, $issuedate, $subject, $body_html, $body_txt, $published, $pubname
    ) = $result->fields;
    $result->Close();

    // security check
    if (!xarSecurityCheck('VieweBulletin', 0, 'Publication', "$pubname:$pid")) return;

    // assemble issue data
    $issue = array(
        'id' => $id,
        'pid' => $pid,
        'issuedate' => $issuedate,
        'subject' => $subject,
        'body_html' => $body_html,
        'body_txt' => $body_txt,
        'published' => $published,
        'pubname' => $pubname
    );

    // success
    return $issue;
}

?>

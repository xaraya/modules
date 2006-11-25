<?php
/**
* Save an updated issue
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
 * save a modified issue
 *
 * @author the eBulletin module development team
 * @param  $args['iid'] the ID
 * @param  $args['compiled_html'] the HTML
 * @param  $args['compiled_txt'] the text
 * @throws BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
 */
function ebulletin_adminapi_updateissue($args)
{
    extract($args);

    // validate vars
    $invalid = array();
    if (!isset($id) || !is_numeric($id)) {
        $invalid[] = 'id';
    }
    if (!isset($pid) || !is_numeric($pid)) {
        $invalid[] = 'pid';
    }
    if (empty($issuedate) || !preg_match("/\d\d\d\d-\d\d-\d\d/", $issuedate)) {
        $invalid[] = 'issuedate';
    }
    if (empty($body_html) && empty($body_txt)) {
        $invalid[] = 'body';
    }

    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
            join(', ', $invalid), 'adminapi', 'updateissue', 'eBulletin');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return;
    }

    // retrieve parent publication
    $pub = xarModAPIFunc('ebulletin', 'user', 'get', array('id' => $pid));
    if (!isset($pub) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return;

    // security check
    if (!xarSecurityCheck('EditeBulletin', 1, 'Publication', "$pub[name]:$pid")) return;

    $published = empty($published) ? 0 : 1;

    // prepare for database
    $dbconn = xarDBGetConn();
    $xartable = xarDBGetTables();
    $issuestable = $xartable['ebulletin_issues'];

    // get new issue ID
    $nextId = $dbconn->GenId($issuestable);

    // generate query
    $query = "
        UPDATE $issuestable
        SET xar_pid = ?,
            xar_issuedate = ?,
            xar_subject = ?,
            xar_body_html = ?,
            xar_body_txt = ?,
            xar_published = ?
        WHERE xar_id = ?
    ";
    $bindvars = array($pid, $issuedate, $subject, $body_html, $body_txt, $published, $id);
    $result = $dbconn->Execute($query,$bindvars);
    if (!$result) return;

    // call create hooks
    $item = $args;
    $item['module'] = 'ebulletin';
    $item['itemtype'] = 1;
    $item['itemid'] = $id;
    xarModCallHooks('item', 'update', $id, $item);

    // success
    return true;
}

?>

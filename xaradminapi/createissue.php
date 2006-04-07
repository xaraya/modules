<?php
/**
* Create a new issue
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
 * create a new ebulletin issue
 *
 * @author the eBulletin module development team
 * @param  $args['iid'] the ID of the issue (to regenerate), OR
 * @param  $args['pid'] the ID of the parent publication AND
 * @param  $args['issuedate'] the date the issue is to carry AND
 * @param  $args['range'] the range of dates to select articles from
 * @returns int
 * @return ebulletin issue ID on success, false on failure
 * @raise BAD_PARAM, NO_PERMISSION, DATABASE_ERROR


* @todo make each publication have the option of its own theme
*/
function ebulletin_adminapi_createissue($args)
{
    extract($args);

    // validate vars
    $invalid = array();
    if (!isset($pid) || !is_numeric($pid)) {
        $invalid[] = 'pid';
    }
    if (empty($issuedate) || !preg_match("/\d\d\d\d-\d\d-\d\d/", $issuedate)) {
        $invalid[] = 'issuedate';
    }

    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
            join(', ', $invalid), 'adminapi', 'createissue', 'eBulletin');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return;
    }

    // retrieve parent publication
    $pub = xarModAPIFunc('ebulletin', 'user', 'get', array('id' => $pid));
    if (xarCurrentErrorType() != XAR_NO_EXCEPTION) return;

    // security check
    if (!xarSecurityCheck('AddeBulletin', 1, 'Publication', "$pub[name]:$pid")) return;

    // generate subject and body
    list(
        $subject,
        $body_html,
        $body_txt
    ) = xarModAPIFunc('ebulletin', 'admin', 'generateissue', array(
        'startday' => $pub['startday'],
        'endday'   => $pub['endday'],
        'subject'  => $pub['subject'],
        'today'    => $issuedate,
        'defaulttheme'  => $pub['theme'],
        'template_html' => $pub['template'],
        'template_txt'  => $pub['template'],
    ));

    // prepare for database
    $dbconn = xarDBGetConn();
    $xartable = xarDBGetTables();
    $issuestable = $xartable['ebulletin_issues'];

    // get new issue ID
    $nextId = $dbconn->GenId($issuestable);

    // generate query
    $query = "
        INSERT INTO $issuestable (
            xar_id,
            xar_pid,
            xar_issuedate,
            xar_subject,
            xar_body_html,
            xar_body_txt,
            xar_published
        ) VALUES (?,?,?,?,?,?,?)
    ";
    $bindvars = array($nextId, $pid, $issuedate, $subject, $body_html, $body_txt, 0);
    $result = $dbconn->Execute($query, $bindvars);
    if (!$result) return;

    // retrieve the ID that was created
    $id = $dbconn->PO_Insert_ID($issuestable, 'xar_id');

    // call create hooks
    $item = $args;
    $item['module']   = 'ebulletin';
    $item['itemtype'] = 1;
    $item['itemid']   = $id;
    xarModCallHooks('item', 'create', $id, $item);

    return $id;
}

?>

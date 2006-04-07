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
function ebulletin_adminapi_regenerateissue($args)
{
    extract($args);

    // validate vars
    $invalid = array();
    if (!isset($id) || !is_numeric($id)) {
        $invalid[] = 'id';
    }

    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
            join(', ', $invalid), 'adminapi', 'regenerateissue', 'eBulletin');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return;
    }

    // retrieve issue
    $issue = xarModAPIFunc('ebulletin', 'user', 'getissue', array('id' => $id));
    if (xarCurrentErrorType() != XAR_NO_EXCEPTION) return;

    // security check
    if (!xarSecurityCheck('EditeBulletin', 1, 'Publication', "$issue[pubname]:$issue[pid]")) return;

    // retrieve parent publication
    $pub = xarModAPIFunc('ebulletin', 'user', 'get', array('id' => $issue['pid']));
    if (xarCurrentErrorType() != XAR_NO_EXCEPTION) return;

    // get vars for templating
    $template_dir = xarModGetVar('ebulletin', 'template_dir');
    $tpl_html = empty($pub['tpl_html']) ? '' : "$template_dir/$pub[tpl_html]";
    $tpl_txt = empty($pub['tpl_txt']) ? '' : "$template_dir/$pub[tpl_txt]";
    $theme = xarModGetVar('ebulletin', 'theme');

    // generate start and end dates
    $beforesign = ($pub['startsign'] == 'before') ? '-' : '+';
    $aftersign = ($pub['endsign'] == 'before') ? '-' : '+';
    // fix bug 5327 by jonathan@parkerhill.com
    $startdate = strtotime($beforesign.$pub['numsago'].' '.$pub['unitsago'], strtotime($issue['issuedate']));
    $enddate = strtotime($aftersign.$pub['numsfromnow'].' '.$pub['unitsfromnow'], strtotime($issue['issuedate']));

    // generate the issue
    list(
        $subject, $body_html, $body_txt
    ) = xarModAPIFunc('ebulletin', 'admin', 'generateissue', array(
        'issueid' => $issue['id'],
        'issuedate' => $issue['issuedate'],
        'startdate' => $startdate,
        'enddate' => $enddate,
        'subject' => $pub['subject'],
        'htmltemplate' => $tpl_html,
        'txttemplate' => $tpl_txt,
        'themename' => $theme,
    ));
    if (xarCurrentErrorType() != XAR_NO_EXCEPTION) return;

    // prepare for database
    $dbconn = xarDBGetConn();
    $xartable = xarDBGetTables();
    $issuestable = $xartable['ebulletin_issues'];

    // generate query
    $query = "
        UPDATE $issuestable
        SET xar_subject = ?,
            xar_body_html = ?,
            xar_body_txt = ?
        WHERE xar_id = ?
    ";
    $bindvars = array($subject, $body_html, $body_txt, $id);
    $result = $dbconn->Execute($query, $bindvars);
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

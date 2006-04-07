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
 * new issue
 * @param  $ 'pid' the pub to create issue for
 */
function ebulletin_admin_newissue($args)
{
    extract($args);

    // get HTTP vars
    if (!xarVarFetch('pid', 'id', $pid, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('invalid', 'array', $invalid, $invalid, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('issuedate', 'str:10:10', $issuedate, $issuedate, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('subject', 'str:1:255', $subject, $subject, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('body_html', 'str', $body_html, $body_html, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('body_txt', 'str', $body_txt, $body_txt, XARVAR_NOT_REQUIRED)) return;

    // validate inputs
    if (empty($pid) || !is_numeric($pid)) {
        $invalid[] = 'pid';
    }
    if (!empty($issuedate) && !preg_match("/^\d\d\d\d-\d\d\-\d\d\$/", $issuedate)) {
        $invalid[] = 'issuedate';
    }

    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
            join(', ', $invalid), 'admin', 'newissue', 'eBulletin');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
            new SystemException($msg));
        return;
    }

    // retrieve parent publication
    $pub = xarModAPIFunc('ebulletin', 'user', 'get', array('id' => $pid));
    if (xarCurrentErrorType() != XAR_NO_EXCEPTION) return;

    // security check
    if (!xarSecurityCheck('AddeBulletin', 1, 'Publication', "$pub[name]:$pid")) return;

    // get defaults for issue vars
    if (empty($issuedate)) $issuedate = date('Y-m-d', time());
    if (empty($subject))   $subject = '';
    if (empty($body_html)) $body_html = '';
    if (empty($body_txt))  $body_txt = '';
    if (empty($published)) $published = false;

    // get other vars
    $datedefinition = array(
        'name'       => 'issuedate',
        'type'       => 'calendar',
        'id'         => 'issuedate',
        'class'      => 'xar-form-textmedium',
        'value'      => time(),
        'dateformat' => '%Y-%m-%d'
    );

    // get hooks
    $hookoutput = xarModCallHooks('item', 'new', '', array(
        'module' => 'ebulletin', 'itemtype' => 1
    ));

    // set template vars
    $data = array();
    $data['pid']       = $pid;
    $data['issuedate'] = $issuedate;
    $data['subject']   = xarVarPrepForDisplay($subject);
    $data['body_html'] = xarVarPrepForDisplay($body_html);
    $data['body_txt']  = xarVarPrepForDisplay($body_txt);
    $data['published'] = $published;

    // set other vars
    $data['pub']            = $pub;
    $data['authid']         = xarSecGenAuthKey();
    $data['invalid']        = $invalid;
    $data['hookoutput']     = $hookoutput;
    $data['datedefinition'] = $datedefinition;

    return $data;
}

?>

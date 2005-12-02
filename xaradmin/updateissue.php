<?php
/**
* Update an issue
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
 * Save a modified issue
 * Form supplied by xarModFunc('ebulletin','admin','editissue')
 *
 * @param  $ 'id' the ID
 * @param  $ 'body_html' the HTML
 * @param  $ 'body_txt' the text
 */
function ebulletin_admin_updateissue($args)
{
    // security check
    if (!xarSecConfirmAuthKey()) return;

    extract($args);

    // get HTTP vars
    if (!xarVarFetch('id', 'int:1:', $id, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('pid', 'int:1:', $pid, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('invalid', 'array', $invalid, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('issuedate', 'str:10:10', $issuedate, date('Y-m-d'), XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('subject', 'str:1:255', $subject, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('body_html', 'str', $body_html, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('body_txt', 'str', $body_txt, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('published', 'checkbox', $published, false, XARVAR_NOT_REQUIRED)) return;

    // validate vars
    $invalid = array();
    if (empty($pid) || !is_numeric($pid)) {
        $invalid['pid'] = 1;
        $pid = '';
    }
    if (empty($issuedate) || !preg_match("/\d\d\d\d-\d\d-\d\d/", $issuedate)) {
        $invalid['issuedate'] = 1;
        $issuedate = '';
    }

    // retrieve parent publication
    $pub = xarModAPIFunc('ebulletin', 'user', 'get', array('id' => $pid));
    if (!isset($pub) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return;

    // security check
    if (!xarSecurityCheck('EditeBulletin', 1, 'Publication', "$pub[name]:$pid")) return;

    // assemble array of data
    $data = array();
    $data['id'] = $id;
    $data['pid'] = $pid;
    $data['issuedate'] = $issuedate;
    $data['subject'] = $subject;
    $data['body_html'] = $body_html;
    $data['body_txt'] = $body_txt;
    $data['published'] = $published;

    // check if we have any errors
    if (count($invalid) > 0) {
        $data['invalid'] = $invalid;
        return xarModFunc('ebulletin', 'admin', 'modifyissue', $data);
    }

    // let API function do the updating
    $id = xarModAPIFunc('ebulletin', 'admin', 'updateissue', $data);
    if (!isset($id) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return;

    // set status message and redirect to issues view page
    xarSessionSetVar('statusmsg', xarML('Issue successfully updated!'));
    xarResponseRedirect(xarModURL('ebulletin', 'admin', 'viewissues'));

    // success
    return true;
}

?>

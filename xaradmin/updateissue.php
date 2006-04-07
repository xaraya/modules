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
#    if (!xarSecConfirmAuthKey()) return;

    extract($args);

    // get HTTP vars
    if (!xarVarFetch('id', 'id', $id)) return;
    if (!xarVarFetch('regen', 'int', $regen, false, XARVAR_NOT_REQUIRED)) return;

    if (!isset($regen)) $regen = false;

    // retrieve issue and publication
    $issue = xarModAPIFunc('ebulletin', 'user', 'getissue', array('id' => $id));
    if (xarCurrentErrorType() != XAR_NO_EXCEPTION) return;
    $pub = xarModAPIFunc('ebulletin', 'user', 'get', array('id' => $issue['pid']));
    if (xarCurrentErrorType() != XAR_NO_EXCEPTION) return;

    // security check
    if (!xarSecurityCheck('EditeBulletin', 1, 'Publication', "$issue[pubname]:$issue[id]")) return;

    if ($regen) {

        list(
            $subject,
            $body_html,
            $body_txt
        ) = xarModAPIFunc('ebulletin', 'admin', 'generateissue', array(
            'startday' => $pub['startday'],
            'endday'   => $pub['endday'],
            'subject'  => $pub['subject'],
            'today'    => $issue['issuedate'],
            'defaulttheme'  => $pub['theme'],
            'template_html' => $pub['template'],
            'template_txt'  => $pub['template'],
        ));
        if (xarCurrentErrorType() != XAR_NO_EXCEPTION) return;

        // set updated vars
        $data = $issue;
        $data['subject']   = $subject;
        $data['body_html'] = $body_html;
        $data['body_txt']  = $body_txt;

    } else {

        if (!xarVarFetch('pid', 'id', $pid)) return;
        if (!xarVarFetch('issuedate', 'str:10:10', $issuedate, date('Y-m-d'), XARVAR_NOT_REQUIRED)) return;
        if (!xarVarFetch('subject', 'str:1:255', $subject, '', XARVAR_NOT_REQUIRED)) return;
        if (!xarVarFetch('body_html', 'str', $body_html, '', XARVAR_NOT_REQUIRED)) return;
        if (!xarVarFetch('body_txt', 'str', $body_txt, '', XARVAR_NOT_REQUIRED)) return;
        if (!xarVarFetch('published', 'checkbox', $published, false, XARVAR_NOT_REQUIRED)) return;

        // validate vars
        $invalid = array();
        if (empty($issuedate) || !preg_match("/\d\d\d\d-\d\d-\d\d/", $issuedate)) {
            $invalid['issuedate'] = 1;
            $issuedate = date('Y-m-d');
        }
        if (empty($subject)) {
            $invalid['subject'] = 1;
            $subject = '';
        }

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
    }

    // let API function do the updating
    xarModAPIFunc('ebulletin', 'admin', 'updateissue', $data);
    if (xarCurrentErrorType() != XAR_NO_EXCEPTION) return;

    // set status and redirect
    xarSessionSetVar('statusmsg', xarML('Issue successfully updated!'));
    xarResponseRedirect(xarModURL('ebulletin', 'admin', 'modifyissue', array('id' => $id)));

    return true;
}

?>

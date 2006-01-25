<?php
/**
* Show an <iframe> in order to display an issue
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
 * show frame for displaying issue
 */
function ebulletin_user_display($args)
{
    extract($args);

    // get HTTP vars
    if (!xarVarFetch('id', 'int:1:', $id)) return;

    // get issue
    $issue = xarModAPIFunc('ebulletin', 'user', 'getissue', array('id' => $id));
    if (empty($issue) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return;

    // security check
    if (!xarSecurityCheck('ReadeBulletin', 0, 'Publication', "$issue[pubname]:$issue[pid]")) return;

    // format some vars
    $issue['published_string'] = ($issue['published']) ? xarML('Yes') : xarML('No');
    $issue['issuedate'] = xarLocaleGetFormattedDate('short', strtotime($issue['issuedate']));

    // url for edit and regenerate
    if (xarSecurityCheck('EditeBulletin', 0, 'Publication', "$issue[pubname]:$issue[pid]")) {
        $issue['editurl'] = xarModURL('ebulletin', 'user', 'modifyissue',
            array('id' => $issue['id'])
        );
        $issue['regenerateurl'] = xarModURL('ebulletin', 'user', 'regenerateissue', array(
            'id' => $issue['id'],
            'authid' => xarSecGenAuthKey(),
            'return' => xarServerGetCurrentURL()
        ));
    }

    // url for delete
    if (xarSecurityCheck('DeleteeBulletin', 0, 'Publication', "$issue[pubname]:$issue[pid]")) {
        $issue['deleteurl'] = xarModURL('ebulletin', 'user', 'deleteissue', array(
            'id' => $issue['id'])
        );
    }

    // url for publish
    if (xarSecurityCheck('AddeBulletin', 0, 'Publication', "$issue[pubname]:$issue[pid]")) {
        $issue['publishurl'] = xarModURL('ebulletin', 'user', 'publishissue', array(
            'id' => $issue['id'])
        );
    }

    // links for the iframe
    if (!empty($issue['body_html'])) {
        $htmlurl = xarModURL('ebulletin', 'user', 'displayissue',
            array('id' => $id, 'displaytype' => 'html')
        );
    }
    if (!empty($issue['body_txt'])) {
        $txturl = xarModURL('ebulletin', 'user', 'displayissue',
            array('id' => $id, 'displaytype' => 'txt')
        );
    }

    // initialize template data
    $data = xarModAPIFunc('ebulletin', 'user', 'menu', array('tab' => 'archive'));

    // set template vars
    $data['id'] = $id;
    $data['issue'] = $issue;
    $data['htmlurl'] = empty($htmlurl) ? '' : $htmlurl;
    $data['txturl'] = empty($txturl) ? '' : $txturl;

    return $data;

}

?>

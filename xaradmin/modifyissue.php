<?php
/**
* Edit an issue
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
 * edit an issue
 */
function ebulletin_admin_modifyissue()
{
    // get HTTP vars
    if (!xarVarFetch('id', 'int:1:', $id)) return;

    // get issue
    $issue = xarModAPIFunc('ebulletin', 'user', 'getissue', array('id' => $id));
    if (empty($issue) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return;

    // security check
    if (!xarSecurityCheck('EditeBulletin', 0, 'Publication', "$issue[pubname]:$issue[pid]")) return;

    // get other vars
    $authid = xarSecGenAuthKey();

    // get date definition
    $datedefinition = array(
        'name' => 'issuedate',
        'type' => 'calendar',
        'id' => 'issuedate',
        'class' => 'xar-form-textmedium',
        'value' => strtotime($issue['issuedate']),
        'dateformat' => '%Y-%m-%d'
    );

    // url for view
    if (xarSecurityCheck('ReadeBulletin', 0, 'Publication', "$issue[pubname]:$issue[pid]")) {
        $issue['viewurl'] = xarModURL('ebulletin', 'admin', 'display', array(
            'id' => $issue['id'])
        );
    }

    // url for edit and regenerate
    if (xarSecurityCheck('EditeBulletin', 0, 'Publication', "$issue[pubname]:$issue[pid]")) {
        $issue['regenerateurl'] = xarModURL('ebulletin', 'admin', 'regenerateissue', array(
            'id' => $issue['id'],
            'authid' => xarSecGenAuthKey(),
            'return' => xarModURL('ebulletin', 'admin', 'viewissues'))
        );
    }

    // url for delete
    if (xarSecurityCheck('DeleteeBulletin', 0, 'Publication', "$issue[pubname]:$issue[pid]")) {
        $issue['deleteurl'] = xarModURL('ebulletin', 'admin', 'deleteissue', array(
            'id' => $issue['id'])
        );
    }

    // url for publish
    if (xarSecurityCheck('AddeBulletin', 0, 'Publication', "$issue[pubname]:$issue[pid]")) {
        $issue['publishurl'] = xarModURL('ebulletin', 'admin', 'publishissue', array(
            'id' => $issue['id'])
        );
    }

    // links for the iframe
    if (!empty($issue['body_html'])) {
        $htmlurl = xarModURL('ebulletin', 'user', 'displayissue',
            array('id' => $id, 'displaytype' => 'html')
        );
    }
    if (!empty($issue['body_html'])) {
        $txturl = xarModURL('ebulletin', 'user', 'displayissue',
            array('id' => $id, 'displaytype' => 'txt')
        );
    }

    // initialize template data
    $data = xarModAPIFunc('ebulletin', 'admin', 'menu');

    // set template vars
    $data['issue'] = $issue;
    $data['htmlurl'] = $htmlurl;
    $data['txturl'] = $txturl;
    $data['authid'] = $authid;
    $data['id'] = $id;
    $data['datedefinition'] = $datedefinition;

    return $data;

}

?>

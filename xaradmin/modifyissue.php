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
    if (xarCurrentErrorType() != XAR_NO_EXCEPTION) return;

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

    // get publication
    $pub = xarModAPIFunc('ebulletin', 'user', 'get', array('id' => $issue['pid']));
    if (xarCurrentErrorType() != XAR_NO_EXCEPTION) return;

    if (xarSecurityCheck('EditeBulletin', 0, 'Publication', "$issue[pubname]:$issue[pid]")) {

        // preview HTML
        if ($pub['html']) {
            $issue['urls'][] = array(
                'url' => xarModURL('ebulletin', 'user', 'displayissue', array(
                    'id' => $issue['id'], 'displaytype' => 'html'
                )),
                'title' => xarML('Preview the HTML version of this issue'),
                'label' => xarML('Preview HTML'),
            );
        }
        $issue['urls'][] = array(
            'url' => xarModURL('ebulletin', 'user', 'displayissue', array(
                'id' => $issue['id'], 'displaytype' => 'txt'
            )),
            'title' => xarML('Preview the text version of this issue'),
            'label' => xarML('Preview Text'),
        );
    }

    if (xarSecurityCheck('DeleteeBulletin', 0, 'Publication', "$issue[pubname]:$issue[pid]")) {

        // delete
        $issue['urls'][] = array(
            'url' => xarModURL('ebulletin', 'admin', 'deleteissue', array(
                'id' => $issue['id']
            )),
            'title' => xarML('Delete this issue.'),
            'label' => xarML('Delete'),
            'onclick' => $issue['published'] ? 'return confirm(\''.xarML('This issue has already been published.  Are you sure you want to delete it?').'\');' : ''
        );
    }

    if (xarSecurityCheck('AddeBulletin', 0, 'Publication', "$issue[pubname]:$issue[pid]")) {

        // regenerate
        $issue['urls'][] = array(
            'url' => xarModURL('ebulletin', 'admin', 'updateissue', array(
                'id' => $issue['id'],
                'regen' => true,
                'authid' => $authid
            )),
            'title' => xarML('Regenerate this issue'),
            'label' => xarML('Regenerate'),
            'onclick' => $issue['published'] ? 'return confirm(\''.xarML('This issue has already been published.  Are you sure you want to regenerate it?').'\');' : ''
        );

        // publish
        $issue['urls'][] = array(
            'url' => xarModURL('ebulletin', 'admin', 'publishissue', array(
                'id' => $issue['id']
            )),
            'title' => xarML('Publish this issue to its regular distribution list.'),
            'label' => xarML('Publish'),
            'onclick' => $issue['published'] ? 'return confirm(\''.xarML('This issue has already been published.  Are you sure you want to publish it again?').'\');' : ''
        );

    }

    if (xarSecurityCheck('AddeBulletin', 0, 'Publication', "$issue[pubname]:$issue[pid]")) {

        // test
        $issue['urls'][] = array(
            'url' => xarModURL('ebulletin', 'admin', 'sendtest', array(
                'id' => $issue['id']
            )),
            'title' => xarML('Send this issue to one recipient.'),
            'label' => xarML('Test'),
        );

    }

    // set template vars
    $data = array();
    $data['issue']          = $issue;
    $data['authid']         = $authid;
    $data['id']             = $id;
    $data['datedefinition'] = $datedefinition;

    return $data;

}

?>

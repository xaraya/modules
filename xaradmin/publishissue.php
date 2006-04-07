<?php
/**
* Publish an issue
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
 * publish issue
 *
 * @param  $ 'id' the id of the item to be publishd
 * @param  $ 'confirm' confirm that this item can be publishd
 */
function ebulletin_admin_publishissue($args)
{
    extract($args);

    // get HTTP vars
    if (!xarVarFetch('id', 'int:1:', $id)) return;
    if (!xarVarFetch('confirm', 'str:1:', $confirm, '', XARVAR_NOT_REQUIRED)) return;

    // get issue
    $issue = xarModAPIFunc('ebulletin', 'user', 'getissue', array('id' => $id));
    if (xarCurrentErrorType() != XAR_NO_EXCEPTION) return;

    // security check
    if (!xarSecurityCheck('AddeBulletin', 1, 'Publication', "$issue[pubname]:$issue[id]")) return;

    // Check for confirmation.
    if (empty($confirm)) {

        // get publication
        $pub = xarModAPIFunc('ebulletin', 'user', 'get', array('id' => $issue['pid']));
        if (xarCurrentErrorType() != XAR_NO_EXCEPTION) return;

        // get list of would-be recipients
        $subscribers = xarModAPIFunc('ebulletin', 'user', 'getsubscriberemails',
            array('pid' => $issue['pid'])
        );
        if (empty($subscribers) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return;
        $subscriberscount = count($subscribers);

        // set template vars
        $data = array();
        $data['id']               = $id;
        $data['issue']            = $issue;
        $data['pub']              = $pub;
        $data['authid']           = xarSecGenAuthKey();
        $data['htmlsize']         = round(strlen($issue['body_html'])/1024, 1);
        $data['txtsize']          = round(strlen($issue['body_txt'])/1024, 1);
        $data['subscribers']      = $subscribers;
        $data['subscriberscount'] = $subscriberscount;

        return $data;
    }

    // security check
    if (!xarSecConfirmAuthKey()) return;

    // call API function to do the publishing
    if (!xarModAPIFunc('ebulletin', 'admin', 'send_issue', array('id' => $id))) return;

    // set status message and return to view
    xarSessionSetVar('statusmsg', xarML('Issue successfully published!'));
    xarResponseRedirect(xarModURL('ebulletin', 'admin', 'viewissues'));

    // success
    return true;
}

?>
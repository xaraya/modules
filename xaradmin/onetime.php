<?php
/**
* One-time Message
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
* one-time message
*
* Send one-time message to a publication's distribution list
*
* @param  $ 'id' the id of the item to be publishd
* @param  $ 'confirm' confirm that this item can be publishd
*/
function ebulletin_admin_onetime($args)
{
    extract($args);

    // get HTTP vars
    if (!xarVarFetch('pid', 'int:1:', $pid)) return;
    if (!xarVarFetch('confirm', 'str:1:', $confirm, '', XARVAR_NOT_REQUIRED)) return;

    // get publication
    $pub = xarModAPIFunc('ebulletin', 'user', 'get', array('id' => $pid));
    if (empty($pub) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return;

    // security check
    if (!xarSecurityCheck('AddeBulletin', 1, 'Publication', "$pub[name]:$pid")) return;

    // Check for confirmation.
    if (empty($confirm)) {

        // get list of would-be recipients
        $subscribers = xarModAPIFunc('ebulletin', 'user', 'getsubscriberemails',
            array('pid' => $pid)
        );
        if (empty($subscribers) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return;
        $subscriberscount = count($subscribers);

        // initialize template data
        $data = xarModAPIFunc('ebulletin', 'admin', 'menu');

        // get vars
        $authid = xarSecGenAuthKey();

        // set template data
        $data['pid'] = $pid;
        $data['pub'] = $pub;
        $data['authid'] = $authid;
        $data['subscribers'] = $subscribers;
        $data['subscriberscount'] = $subscriberscount;

        return $data;
    }

    // security check
    if (!xarSecConfirmAuthKey()) return;

    // get additional HTTP vars
    if (!xarVarFetch('body_html', 'str:0:', $body_html, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('body_txt', 'str:0:', $body_txt, '', XARVAR_NOT_REQUIRED)) return;

    // call API function to do the sending
    if (!xarModAPIFunc('ebulletin', 'admin', 'send_onetime', array('onetime' => true, 'pid' => $pid, 'body_html' => $body_html, 'body_txt' => $body_txt))) return;

    // set status message and return to view
    xarSessionSetVar('statusmsg', xarML('One-time message successfully sent!'));
    xarResponseRedirect(xarModURL('ebulletin', 'admin', 'view'));

    // success
    return true;
}

?>
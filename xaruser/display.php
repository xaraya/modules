<?php
/**
 * Messages Module
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Messages Module
 * @link http://xaraya.com/index.php/release/6.html
 * @author XarayaGeek
 */
function messages_user_display( )
{
    // Security check
    if (!xarSecurityCheck('ViewMessages', 0)) {
        return $data['error'] = xarML('You are not permitted to view messages.');
    }


    $read_messages = xarModGetUserVar('messages','read_messages');
    if (!empty($read_messages)) {
        $read_messages = unserialize($read_messages);
    } else {
        $read_messages = array();
    }

    $messages = xarModAPIFunc('messages', 'user', 'getall', array());

    if (is_array($messages)) {

        krsort($messages);

        $data['messages'] = $messages;
        $data['header_attachment_image'] = xarTplGetImage('attachment.png');
        $data['header_status_image']     = xarTplGetImage('check_read.gif');
        $data['unread'] = xarModAPIFunc('messages','user','count_unread');
        $data['sent']   = xarModAPIFunc('messages','user','count_sent');
        $data['total']  = xarModAPIFunc('messages','user','count_total');

    } else {
        $list = array();
    }

    if (xarUserIsLoggedIn()) {
        if (!xarVarFetch('away','str',$away,null,XARVAR_NOT_REQUIRED)) return;
        if (isset($away)) {
            xarModSetUserVar('messages','away_message',$away);
        }
        $data['away_message'] = xarModGetUserVar('messages','away_message');
    } else {
        $data['away_message'] = '';
    }

    return $data;
}

?>

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
    if (!xarSecurityCheck('ReadMessages', 0)) {
        return $data['error'] = xarML('You are not permitted to read messages.');
    }

    if (!xarVarFetch('folder', 'enum:inbox:sent:drafts', $folder, 'inbox')) return;

    $read_messages = xarModUserVars::get('messages','read_messages');
    if (!empty($read_messages)) {
        $read_messages = unserialize($read_messages);
    } else {
        $read_messages = array();
    }

    $messages = xarModAPIFunc('messages', 'user', 'getall', array('folder' => $folder));

    if (is_array($messages)) {

        krsort($messages);

        $data['messages']                = $messages;
        $data['header_attachment_image'] = xarTplGetImage('attachment.png');
        $data['header_status_image']     = xarTplGetImage('check_read.gif');
        $data['unread']                  = xarModAPIFunc('messages','user','count_unread');
        $data['sent']                    = xarModAPIFunc('messages','user','count_sent');
        $data['total']                   = xarModAPIFunc('messages','user','count_total');
        $data['drafts']                  = xarModAPIFunc('messages','user','count_drafts');

    } else {
        $list = array();
    }
    if (xarUserIsLoggedIn()) {
        if (!xarVarFetch('away','str',$away,null,XARVAR_NOT_REQUIRED)) return;
        if (isset($away)) {
            xarModUserVars::set('messages','away_message',$away);
        }
        $data['away_message'] = xarModUserVars::get('messages','away_message');
    } else {
        $data['away_message'] = '';
    }

    $data['folder'] = xarML(ucfirst($folder));

    return $data;
}

?>

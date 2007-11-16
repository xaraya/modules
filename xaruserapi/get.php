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
function messages_userapi_get( $args )
{

    extract( $args );

    if (!isset($mid) || empty($mid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                                 'message_id', 'userapi', 'get', 'messages');
        xarErrorSet(XAR_USER_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return;
    }

	if(!isset($status) || !in_array($status, array(1,2,3))){
		$status = 2;
	}

    $list = xarModAPIFunc('comments',
                           'user',
                           'get_one',
                            array('cid' => $mid, 'status' => $status));

    $read_messages = xarModGetUserVar('messages','read_messages');
    if (!empty($read_messages)) {
        $read_messages = unserialize($read_messages);
    } else {
        $read_messages = array();
    }

    $messages = array();

    foreach ($list as $key => $node) {
        $message['mid']           = $node['xar_cid'];
        $message['sender']        = $node['xar_author'];
        $message['sender_id']     = $node['xar_uid'];
        $message['recipient']    = xarUserGetVar('name',$node['xar_objectid']);
        $message['recipient_id'] = $node['xar_objectid'];
        $message['posting_host']  = $node['xar_hostname'];
        $message['raw_date']      = $node['xar_datetime'];
        $message['date']          = xarLocaleFormatDate('%A, %B %d @ %H:%M:%S', $node['xar_datetime']);
        $message['subject']       = $node['xar_title'];
        $message['body']          = $node['xar_text'];
        $message['draft']        = ($node['xar_status'] == 1 ? true : false);

        $message['status_image'] = xarTplGetImage('draft.gif');
        $message['status_alt']   = xarML('draft');


        $message['user_link']     = xarModURL('roles','user','display',
                                               array('uid' => $node['xar_uid']));
        $message['view_link']     = xarModURL('messages','user', 'view',
                                               array('mid'    => $node['xar_cid']));

        $message['reply_link']    = xarModURL('messages','user','send',
                                               array('action' => 'reply',
                                                     'mid'    => $node['xar_cid']));

        $message['delete_link']   = xarModURL('messages','user','delete',
                                               array('mid'    => $node['xar_cid'],
                                                     'action' => 'check'));

        $messages[0] = $message;
    }

    return $messages;
}


?>

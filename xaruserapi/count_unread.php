<?php
function messages_userapi_count_unread()
{

    $total =& xarModAPIFunc('comments',
                            'user',
                            'get_count',
                             array('modid'      => xarModGetIDFromName('messages'),
                                   'objectid'   => xarUserGetVar('uid')));

    $read_messages = xarModGetUserVar('messages','read_messages');
    if (!empty($read_messages)) {
        $read_messages = unserialize($read_messages);
    } else {
        $read_messages = array();
    }

    $total_read = count($read_messages);

    /*
     * if total is zero or it's <= total_read,
     * then total unread equals zero
     */

    if (!$total || $total <= $total_read) {
        $total = 0;
    } else {
        $total -= $total_read;
    }

    return $total;
}
?>

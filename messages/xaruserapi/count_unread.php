<?php
function messages_userapi_count_unread()
{

    $total =& xarModAPIFunc('comments',
                            'user',
                            'get_count',
                             array('modid'      => xarModGetIDFromName('messages'),
                                   'objectid'   => xarUserGetVar('uid')));

    $total_read = count(unserialize(xarModGetUserVar('messages','read_messages')));

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

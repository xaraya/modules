<?php

function messages_userapi_count_outbox()
{
    // Load the comments api so we can have the defines
    xarModAPILoad('comments','user');

    $total =& xarModAPIFunc('comments',
                            'user',
                            'get_author_count',
                             array('modid'  => xarModGetIDFromName('messages'),
                                   'author' => xarUserGetVar('uid'),
                                   'status' => _COM_STATUS_OFF
                                  )
                            );

    return $total;
}

?>
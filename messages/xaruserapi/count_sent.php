<?php
function messages_userapi_count_sent()
{

    $total =& xarModAPIFunc('comments',
                            'user',
                            'get_author_count',
                             array('modid'  => xarModGetIDFromName('messages'),
                                   'author' => xarUserGetVar('uid')));

    return $total;
}
?>

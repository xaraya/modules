<?php
function messages_userapi_count_total()
{
    $total =& xarModAPIFunc('comments',
                            'user',
                            'get_count',
                             array('modid'      => xarModGetIDFromName('messages'),
                                   'objectid'   => xarUserGetVar('uid')));

    return $total;
}
?>

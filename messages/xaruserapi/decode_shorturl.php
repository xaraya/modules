<?php

function messages_userapi_decode_shorturl( $params ) 
{


    if ( $params[0] != 'messages' )
        return;

    if (empty($params[1]))
        $params[1] = '';

    switch ($params[1]) {
        case 'Outbox':
            return array('send', array());
            break;
        case 'Trash':
            return array('delete', array());
            break;
        default:
        case 'Inbox':
            if (isset($params[2])) {
                return array('view', array('mid' => $params[2]));
            } else {
                return array('display', array());
            }
            break;
    }

}
?>

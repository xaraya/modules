<?php
function messages_userapi_create( $args )
{
    extract($args);

    if (!isset($subject)) {
        $msg = xarML('Missing #(1) for #(2) function #(3)() in module #(4)',
                     'subject', 'userapi', 'create', 'messages');
        xarExceptionSet(XAR_USER_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return;
    }

    if (!isset($body)) {
        $msg = xarML('Missing #(1) for #(2) function #(3)() in module #(4)',
                     'body', 'userapi', 'create', 'messages');
        xarExceptionSet(XAR_USER_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return;
    }

    if (!isset($receipient)) {
        $msg = xarML('Missing #(1) for #(2) function #(3)() in module #(4)',
                     'receipient', 'userapi', 'create', 'messages');
        xarExceptionSet(XAR_USER_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return;
    }

    // check the authorisation key
    if (!xarSecConfirmAuthKey()) return; // throw back

    return xarModAPIFunc('comments',
                         'user',
                         'add',
                          array('modid'       => xarModGetIDFromName('messages'),
                                'objectid'    => $receipient,
                                'title'       => $subject,
                                'comment'     => $body,
                                'author'      => xarUserGetVar('uid')));
}

?>

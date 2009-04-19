<?php

function registration_admin_sendtest()
{
    if (!xarSecurityCheck('EditRegistration')) return;
    if(!xarVarFetch('for',   'str', $data['for']   , '', XARVAR_DONT_SET)) {return;}
    $data['email'] = xarUserGetVar('email');
    $data['realname'] = xarUserGetVar('name');
    if (xarModIsAvailable('mailer')) {
        if ($data['for'] == 'admin') {
            $data['result'] = xarModAPIFunc('mailer','user','send',
                            array(
                                'name'               => xarModVars::get('registration', 'adminmessage'),
                                'recipientname'      => xarModVars::get('mail', 'adminname'),
                                'recipientaddress'   => xarModVars::get('mail', 'adminmail'),
                            )
                        );
        } elseif ($data['for'] == 'user') {
            $data['result'] = xarModAPIFunc('mailer','user','send',
                            array(
                                'name'               => xarModVars::get('registration', 'usermessage'),
                                'recipientname'      => $data['realname'],
                                'recipientaddress'   => $data['email'],
                                'data'               => $data,
                            )
                        );
        }
    } else {
        $data['result'] = 99;
    }
    return $data;
}
?>
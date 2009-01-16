<?php
/**
 * Test function
 *
 */

    function mailer_user_test()
    {
        // Security Check
        if (!xarSecurityCheck('ReadMailer')) return;

        if (!xarVarFetch('phase', 'str:1:100', $phase, '', XARVAR_NOT_REQUIRED, XARVAR_PREP_FOR_DISPLAY)) return;
        if (!xarVarFetch('message_id', 'int', $data['message_id'], 0, XARVAR_NOT_REQUIRED, XARVAR_PREP_FOR_DISPLAY)) return;
        if (!xarVarFetch('locale', 'str:1:100', $data['locale'], 'en_US.utf-8', XARVAR_NOT_REQUIRED, XARVAR_PREP_FOR_DISPLAY)) return;
        if (!xarVarFetch('recipientaddress', 'str:1:100', $data['recipientaddress'], '', XARVAR_NOT_REQUIRED, XARVAR_PREP_FOR_DISPLAY)) return;

        if ($phase == 'submit') {
            if (!xarSecConfirmAuthKey()) return;
            if (!empty($data['message_id'])) {
                $data['result'] = xarModAPIFunc('mailer','user','send',
                                array(
                                    'id'               => $data['message_id'],
                                    'locale'           => $data['locale'],
                                    'recipientname'    => xarModVars::get('mailer','defaultsendername'),
                                    'recipientaddress' => $data['recipientaddress'],
                                )
                            );
                $data['sent'] = true;
            }
        } else {
            $data['sent'] = false;
        }
        return $data;
    }

?>
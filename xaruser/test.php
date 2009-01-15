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
        if ($phase == 'submit') {
            if (!xarSecConfirmAuthKey()) return;
            $data['result'] = xarModAPIFunc('mailer','user','send',
                            array(
                                'id'               => 1,
                                'recipientname'    => xarModVars::get('mailer','defaultsendername'),
                                'recipientaddress' => xarModVars::get('mailer','defaultsenderaddress'),
                            )
                        );
            $data['sent'] = true;
        } else {
            $data['sent'] = false;
        }
        return $data;
    }

?>
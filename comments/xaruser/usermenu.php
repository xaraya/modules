<?php

function comments_user_usermenu() 
{

    // Security Check
    if (xarSecurityCheck('Comments-Read',0)) {

        $phase = xarRequestGetVar('phase');

        xarTplSetPageTitle(xarModGetVar('themes', 'SiteName').' :: '.
                           xarVarPrepForDisplay(xarML('Comments'))
                           .' :: '.xarVarPrepForDisplay(xarML('Your Account Preferences')));

        if (empty($phase)){
            $phase = 'menu';
        }

        switch(strtolower($phase)) {
        case 'menu':

            $icon = xarTplGetImage('comments.gif', 'comments');
            $data = xarTplModule('comments','user', 'usermenu_icon',
                array('icon' => $icon,
                      'usermenu_form_url' => xarModURL('comments', 'user', 'usermenu', array('phase' => 'form'))
                     ));
            break;

        case 'form':

            $settings = xarModAPIFunc('comments','user','getoptions');
            $settings['max_depth'] = _COM_MAX_DEPTH - 1;
            $authid = xarSecGenAuthKey();
            $data = xarTplModule('comments','user', 'usermenu_form', array('authid'   => $authid,
                                                                           'settings' => $settings));
            break;

        case 'update':

            $settings = xarVarCleanFromInput('settings');

            if (!isset($settings) || count($settings) <= 0) {
                $msg = xarML('Settings passed from form are empty!');
                xarExceptionSet(XAR_USER_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
                return;
            }

            // Confirm authorisation code.
            if (!xarSecConfirmAuthKey())
                return;

            xarModAPIFunc('comments','user','setoptions',$settings);

            // Redirect
            xarResponseRedirect(xarModURL('roles', 'user', 'account'));

            break;
        }

        return $data;
    }

}

?>
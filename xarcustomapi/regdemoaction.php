<?php

/* args for pageaction processing function
    $ret = xarMod::apiFunc('xarpages','customapi','regdemo_process',array( 'in'=>&$inobject, 'inprop'=>&$inobject->properties, 'out'=>&$outobject, 'outprop'=>&$outobject->properties))

*/

function xarpages_customapi_regdemoaction( $args )
{
    return;
}

function pageform_regdemoaction_process( &$inobj, &$outobj )
{
    $inprop = &$inobj->properties;
    $outprop = &$outobj->properties;
//echo "HELLOW ORLD";
//$inprop['email']->invalid = "HEOOOOOOO";
//$in->properties['message']->invalid = "GDADFSASDFASDF";
//die();
//return 1; 
    // determine state of this create user
    $state = xarMod::apiFunc('registration','user','createstate' );
    //echo "state [$state]"; die();
    
    // actually create the user
    $email = $inprop['email']->getValue();
    $pass = $inprop['password']->getValue();
    $uid = xarMod::apiFunc('registration','user','createuser',
        array(  'username'  => $email,
                'realname'  => $email,
                'email'     => $email,
                'pass'      => $pass,
                'state'     => $state ));
    if (!$uid) {
        $outprop['message']->invalid = 'Cannot create new user account';
        return 1;
    }

    // send out notifications
    $ret = xarMod::apiFunc('registration','user','createnotify',
        array(  'username'  => $email,
                'realname'  => $email,
                'email'     => $email,
                'pass'      => $pass,
                'uid'       => $uid,
                'state'     => $state));
    if (!$ret) {
        $outprop['message']->invalid = 'Error sending email notifications';
        return 1;
    }
/*
    if ($state==ROLES_STATE_ACTIVE) {
        // log in and redirect
        xarMod::apiFunc('authsystem', 'user', 'login', array( 'uname' => $email, 'pass' => $pass, 'rememberme' => 0));
        $redirect=xarServer::getBaseURL();
        xarResponse::Redirect($redirect);
    }
*/
    // ok, then pass state to next form
    $outprop['state']->setValue($state);
    return 1;
}

?>
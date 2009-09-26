<?php

/* example custom functions for pageform EXAMPLE2 -- alternative user registration
    require only email and password from user
    
    assumes pageaction page named "ex2action"
    and you have specified "validate" for validation_func and "process" for processing_func names
*/

/* this function is a placeholder that lets us easily load this file
*/
function xarpages_customapi_ex2action( $args )
{
    return;
}

/* pageform validation function
    note, we can assume that the objects checkInput method has already been called 
    to fetch the posted vars and property validation
    This function does further validation and messaging
    - if email property not valid, provide a nicer message
    - use api to check email not already registered
    - check passwords
    note: return value 0 will go back to form for user to change input, 
          return value 1 will continue to next page, we do if all is ok
*/
function pageform_ex2action_validate( &$inobj )
{
    // extract object fields into local arrays
    pageform_obj2arrays( $inobj, $values, $invalids );

    // CHECK NEW USER ARGS
    $isvalid = true;
    
    // check email (if not already flagged)
    if (!empty($invalids['email'])) {
        // better message than the default property one
        $invalids['email'] = "Please enter a valid email address";
    } 
    else {
        $invalids['email'] = xarMod::apiFunc('registration','user','checkvar', array('type'=>'email', 'var'=>$values['email']));
        if (!empty($invalids['email'])) $isvalid = true;
    }
    
    // check passwords
    if (empty($invalids['password'])) {
        $invalids['password'] = xarMod::apiFunc('registration','user','checkvar', array('type'=>'pass1', 'var'=>$values['password'] ));
        if (empty($invalids['password'])) {
            $invalids['password_again'] = xarMod::apiFunc('registration','user','checkvar', array('type'=>'pass2', 'var'=>array($values['password'],$values['password_again']) ));
        }
    }
    if (!empty($invalids['password'])) {
        $invalids['password_again'] = $invalids['password'];
    }

    // put local values back into object for return
    $isvalid = pageform_arrays2obj( $values, $invalids, $inobj );
    
    return $isvalid;
}

/* pageform processing function
    note, we can assume all input values are valid
    - create new user account using email
    - send out email notifications
    - log the user in
    note: return value 0 will go back to form for user to change input, 
          return value 1 will continue to next page, we do if all is ok, and also on fatal errors
    
    note: in practice its better to use the authemail module to login using email address, 
    and not use email for username, 
    because if user changes his email address that would not change the user name
*/
function pageform_ex2action_process( &$inobj, &$outobj )
{
    // extract object fields into local arrays
    pageform_obj2arrays( $inobj, $values, $invalids );
    pageform_obj2arrays( $outobj, $outvalues, $outinvalids );
    
    // CREATE USER
    // determine state of this create user
    $state = xarMod::apiFunc('registration','user','createstate' );
    
    $email = $values['email'];
    $pass = $values['password'];
    $username = $email;
    
    // find a unique user name (not used for log in)
    //do {
    //  $username = time();
    //  $inval = xarMod::apiFunc('registration','user','checkvar', array('type'=>'username', 'var'=>$username));
    //} while (!empty($inval));
    
    
    // actually create the user
    $uid = xarMod::apiFunc('registration','user','createuser',
        array(  'username'  => $username,
                'realname'  => $email,
                'email'     => $email,
                'pass'      => $pass,
                'state'     => $state ));
    if (!$uid) {
        $outvalues['message'] = 'Cannot create new user account';
    }
    else {
        // send out notifications
        $ret = xarMod::apiFunc('registration','user','createnotify',
            array(  'username'  => $username,
                    'realname'  => $email,
                    'email'     => $email,
                    'pass'      => $pass,
                    'uid'       => $uid,
                    'state'     => $state));
        if (!$ret) {
            $outvalues['message'] = 'Error sending email notifications';
        }
        else {
            // log in
            xarMod::apiFunc('authsystem', 'user', 'login', array( 'uname' => $username, 'pass' => $pass, 'rememberme' => 0));
            $outvalues['message'] = "user account successfully created. username [$username] uid [$uid]";
        }
    }

    // put local values back into objects for return
    pageform_arrays2obj( $values, $invalids, $inobj );
    pageform_arrays2obj( $outvalues, $outinvalids, $outobj );
    
    return true;
}

?>
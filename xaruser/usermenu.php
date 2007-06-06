<?php
function members_user_usermenu($args)
{

    // Security check
    if (!xarSecurityCheck('ViewMembers')) return;
    extract($args);
    if(!xarVarFetch('phase','notempty', $phase, 'menu', XARVAR_NOT_REQUIRED)) {return;}
    xarTplSetPageTitle(xarVarPrepForDisplay(xarML('Your Account Preferences')));
    $data = array(); $hooks = array();
    switch(strtolower($phase)) {
        case 'menu':
            $current = xarModURL('members', 'user', 'account', array('moduleload' => 'members'));
            $data = xarTplModule('members','user', 'user_menu_icon', array('current'      => $current));
            break;
        case 'form':

//          $stub = basename(xarServerGetCurrentURL());
            if(!xarVarFetch('tab','str', $stub, 'tab1', XARVAR_NOT_REQUIRED)) {return;}

            switch(strtolower($stub)) {
                case 'form':
                    $properties = null;
                    $withupload = (int) FALSE;
                    if (xarModIsAvailable('dynamicdata')) {
                        // get the Dynamic Object defined for this module (and itemtype, if relevant)
                        $object = xarModAPIFunc('dynamicdata','user','getobject',
                                                 array('module' => 'roles'));
                        if (isset($object) && !empty($object->objectid)) {
                            // get the Dynamic Properties of this object
                            $properties =& $object->getProperties();
                        }

                        if (is_array($properties)) {
                            foreach ($properties as $key => $prop) {
                                if (isset($prop->upload) && $prop->upload == TRUE) {
                                    $withupload = (int) TRUE;
                                }
                            }
                        }
                    }
                    unset($properties);
                    // get some roles properties, might be useful
                    $uname = xarUserGetVar('uname');
                    $name = xarUserGetVar('name');
                    $id = xarUserGetVar('id');
                    $email = xarUserGetVar('email');
                    $role = xarUFindRole($uname);
                    $home = $role->getHome();
                    $authid = xarSecGenAuthKey();
                    $submitlabel = xarML('Submit');
                    $item['module'] = 'roles';

                    $hooks = xarModCallHooks('item','modify',$id,$item);
                    if (isset($hooks['dynamicdata'])) {
                        unset($hooks['dynamicdata']);
                    }

                    $data = xarTplModule('members','user', 'user_menu_form',
                                          array('authid'       => $authid,
                                          'withupload'   => $withupload,
                                          'name'         => $name,
                                          'uname'        => $uname,
                                          'home'         => $home,
                                          'hooks'        => $hooks,
                                          'emailaddress' => $email,
                                          'submitlabel'  => $submitlabel,
                                          'id'          => $id));
                    break;

                case 'tab2':
                    $data = xarTplModule('members','user', 'user_menu_tab2');
                    break;
            }
            break;

        case 'updatebasic':
            if(!xarVarFetch('id',   'isset', $id,     NULL, XARVAR_DONT_SET)) return;
            if(!xarVarFetch('name',  'isset', $name,    NULL, XARVAR_DONT_SET)) return;
            if(!xarVarFetch('email', 'isset', $email,   NULL, XARVAR_DONT_SET)) return;
            if(!xarVarFetch('home',  'isset', $home,    NULL, XARVAR_DONT_SET)) return;
            if(!xarVarFetch('pass1', 'isset', $pass1,   NULL, XARVAR_DONT_SET)) return;
            if(!xarVarFetch('pass2', 'isset', $pass2,   NULL, XARVAR_DONT_SET)) return;
            $uname = xarUserGetVar('uname');
            // Confirm authorisation code.
            if (!xarSecConfirmAuthKey()) return;
            if (!empty($pass1)){
                $minpasslength = xarModGetVar('roles', 'minpasslength');
                if (strlen($pass2) < $minpasslength) {
                    $msg = xarML('Your password must be #(1) characters long.', $minpasslength);
                    xarErrorSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
                    return;
                }
                // Check to make sure passwords match
                if ($pass1 == $pass2){
                    $pass = $pass1;
                } else {
                    $msg = xarML('The passwords do not match');
                    xarErrorSet(XAR_USER_EXCEPTION, 'BAD_DATA', new DefaultUserException($msg));
                    return;
                }
                $oldemail = xarUserGetVar('email');
                // The API function is called.
                if(!xarModAPIFunc('roles',
                                  'admin',
                                  'update',
                                   array('id' => $id,
                                         'uname' => $uname,
                                         'name' => $name,
                                         'home' => $home,
                                         'email' => $oldemail,
                                         'state' => ROLES_STATE_ACTIVE,
                                         'pass' => $pass))) return;
            }
            if (!empty($email)){
                // Steps for changing email address.
                // 1) Validate the new email address for errors.
                // 2) Log user out.
                // 3) Change user status to 2 (if validation is set as option)
                // 4) Registration process takes over from there.

                // Step 1
                $emailcheck = xarModAPIFunc('roles',
                                            'user',
                                            'validatevar',
                                            array('var' => $email,
                                                  'type' => 'email'));

                if ($emailcheck == false) {
                        $msg = xarML('There is an error in the supplied email address');
                        xarErrorSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
                        return;
                }

                if(xarModGetVar('roles','uniqueemail')) {
                    // check for duplicate email address
                    $user = xarModAPIFunc('roles',
                                          'user',
                                          'get',
                                           array('email' => $email));
                    if ($user != false) {
                        unset($user);
                        $msg = xarML('That email address is already registered.');
                        xarErrorSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
                        return;
                    }
                }

                // check for disallowed email addresses
                $disallowedemails = xarModGetVar('roles','disallowedemails');
                if (!empty($disallowedemails)) {
                    $disallowedemails = unserialize($disallowedemails);
                    $disallowedemails = explode("\r\n", $disallowedemails);
                    if (in_array ($email, $disallowedemails)) {
                        $msg = xarML('That email address is either reserved or not allowed on this website');
                        xarErrorSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
                        return;
                    }
                }
                // Step 3
                $requireValidation = xarModGetVar('roles', 'requirevalidation');
                if ((!xarModGetVar('roles', 'requirevalidation')) || (xarUserGetVar('uname') == 'admin')){
                    // The API function is called.
                    if(!xarModAPIFunc('roles',
                                      'admin',
                                      'update',
                                       array('id' => $id,
                                             'uname' => $uname,
                                             'name' => $name,
                                             'home' => $home,
                                             'email' => $email,
                                             'state' => ROLES_STATE_ACTIVE))) return;
                } else {

                    // Step 2
                    // Create confirmation code and time registered
                    $confcode = xarModAPIFunc('roles',
                                              'user',
                                              'makepass');

                    // Step 3
                    // Set the user to not validated
                    // The API function is called.
                    if(!xarModAPIFunc('roles',
                                      'admin',
                                      'update',
                                       array('id'      => $id,
                                             'uname'    => $uname,
                                             'name'     => $name,
                                             'home'     => $home,
                                             'email'    => $email,
                                             'valcode'  => $confcode,
                                             'state'    => ROLES_STATE_NOTVALIDATED))) return;
                    // Step 4
                    //Send validation email
                    if (!xarModAPIFunc( 'roles',
                                        'admin',
                                        'senduseremail',
                                        array('id' => array($id => '1'), 'mailtype' => 'validation'))) {
                        $msg = xarML('Problem sending confirmation email');
                        xarErrorSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
                    }
                    // Step 5
                    // Log the user out. This needs to happen last
                    xarUserLogOut();
                }
            } else {
                $email = xarUserGetVar('email');
                // The API function is called.
                if(!xarModAPIFunc('roles',
                                  'admin',
                                  'update',
                                   array('id' => $id,
                                         'uname' => $uname,
                                         'name' => $name,
                                         'home' => $home,
                                         'email' => $email,
                                         'state' => ROLES_STATE_ACTIVE))) return;
            }

            // Redirect
            xarResponseRedirect(xarModURL('members', 'user', 'account'));
            return true;
    }
    return $data;
}
?>

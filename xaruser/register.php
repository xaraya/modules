<?php
/**
 * Register a new user
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Registration module
 * @link http://xaraya.com/index.php/release/30205.html
 */
/**
 * Let a new user register
 *
 * Multiple steps to create a new user, as follows:
 *  - get user to agree to terms and conditions (if required)
 *  - get initial information from user
 *  - send confirmation email to user (if required)
 *  - obtain confirmation response from user
 *  - obtain administration permission for account (if required)
 *  - activate account
 *  - send welcome email (if required)
 *
 * @author  Marc Lutolf <marcinmilan@xaraya.com>
 * @author Jo Dalle Nogare
 * @TODO jojodee - rethink and provide cleaner separation between roles, authsystem/authentication and registration
 */
function registration_user_register()
{
    // Security check
    if (!xarSecurityCheck('ViewRegistration')) return;

    //If a user is already logged in, no reason to see this.
    //We are going to send them to their account.
    if (xarUserIsLoggedIn()) {
        xarResponseRedirect(xarModURL('registration',
                                      'user',
                                      'terms'));
       return true;
    }
    $allowregistration = xarModGetVar('registration', 'allowregistration');
    if ($allowregistration != true) {
        $msg = xarML('Registration has been suspended');
        xarErrorSet(XAR_USER_EXCEPTION, 'NO_PERMISSION', new DefaultUserException($msg));
        return;
    }

    xarTplSetPageTitle(xarML('New Account'));
    if (!xarVarFetch('phase','str:1:100',$phase,'request',XARVAR_NOT_REQUIRED)) return;

    switch(strtolower($phase)) {

        case 'choices':
            xarTplSetPageTitle(xarML('Log In'));
            $loginlabel = xarML('Sign In');
            $data = xarTplModule('authsystem','user', 'choices', array('loginlabel' => $loginlabel));
            break;

        case 'checkage':
            $minage = xarModGetVar('registration', 'minage');
            $submitlink=xarModURL('registration','user','register',array('phase'=>'registerform'));
            $data = xarTplModule('registration','user', 'checkage', array('minage'    => $minage,'submitlink'=>$submitlink));
            break;

        case 'registerform': //Make this default now login is handled by authsystem
        default:
            // authorisation code
            $authid = xarSecGenAuthKey();

            // current values (none)
            $values = array('username' => '',
                            'realname' => '',
                            'email'    => '',
                            'pass1'    => '',
                            'pass2'    => '');

            // invalid fields (none)
            $invalid = array();

            // dynamic properties (if any)

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
                if (isset($properties)) {
                    foreach ($properties as $key => $prop) {
                        if (isset($prop->upload) && $prop->upload == TRUE) {
                            $withupload = (int) TRUE;
                        }
                    }
                }
            }
            /* Call hooks here, others than just dyn data
             * We pass the phase in here to tell the hook it should check the data
             */
            $item['module'] = 'registration';
            $item['itemid'] = '';
            $item['values'] = $values;
            $item['phase'] = $phase;
            $hooks = xarModCallHooks('item', 'new', '', $item);

            if (empty($hooks)) {
                $hookoutput = array();
            } else {
                /* You can use the output from individual hooks in your template too, e.g. with
                 * $hookoutput['categories'], $hookoutput['dynamicdata'], $hookoutput['keywords'] etc.
                 */
                $hookoutput = $hooks;
            }

            $data = xarTplModule('registration','user', 'registerform', array('authid' => $authid,
                                                                       'values'     => $values,
                                                                       'invalid'    => $invalid,
                                                                       'properties' => $properties,
                                                                       'hookoutput' => $hookoutput,
                                                                       'withupload' => isset($withupload) ? $withupload : (int) FALSE,
                                                                       'userlabel'  => xarML('New User')));
            break;

        case 'checkregistration':

            if (!xarVarFetch('username','str:1:100',$username,'',XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('realname','str:1:100',$realname,'',XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('pass1','str:4:100',$pass1,'',XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('pass2','str:4:100',$pass2,'',XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('email','str:1:100',$email,'',XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('agreetoterms','checkbox',$agreetoterms,false,XARVAR_NOT_REQUIRED)) return;

            // Confirm authorisation code.
            if (!xarSecConfirmAuthKey()) return;

            // TODO: check behind proxies too ?
            // check if the IP address is banned, and if so, throw an exception :)
            $ip = xarServerGetVar('REMOTE_ADDR');
            $disallowedips = xarModGetVar('registration','disallowedips');
            if (!empty($disallowedips)) {
                $disallowedips = unserialize($disallowedips);
                $disallowedips = explode("\r\n", $disallowedips);
                if (in_array ($ip, $disallowedips)) {
                    $msg = xarML('Your IP is on the banned list');
                    xarErrorSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
                    return;
                }
            }

            // current values (in case some field is invalid, we'll return to the previous template)
            // Pass back all values again so the user only has to type in incorrect values that are highlighted
            $values = array('username' => $username,
                            'realname' => $realname,
                            'email'    => $email,
                            'pass1'    => $pass1,
                            'pass2'    => $pass2);

            /* Call hooks here, others than just dyn data
             * We pass the phase in here to tell the hook it should check the data
             */
            $item = array();
            $item['module'] = 'registration';
            $item['itemid']='';
            $item['values'] = $values; // TODO: this includes the password. Do we want this?
            $item['phase'] = $phase;
            $hooks = xarModCallHooks('item', 'new','', $item);

            if (empty($hooks)) {
                $hookoutput = array();
            } else {
                 $hookoutput = $hooks;
            }

            // invalid fields (we'll check this below)
            $invalid = array();

            // check if the username is empty
            if (empty($username)) {
                $invalid['username'] = xarML('You must provide a preferred username to continue.');

            // check for spaces in the username
            } elseif (preg_match("/[[:space:]]/",$username)) {
                $invalid['username'] = xarML('There is a space in the username');

            // check the length of the username
            } elseif (strlen($username) > 255) {
                $invalid['username'] = xarML('Your username is too long.');

            // check for spaces in the username (again ?)
            } elseif (strrpos($username,' ') > 0) {
                $invalid['username'] = xarML('There is a space in your username');

            } else {
                // check for duplicate usernames
                $user = xarModAPIFunc('roles',
                                      'user',
                                      'get',
                                       array('uname' => $username));
                if ($user != false) {
                    unset($user);
                    $invalid['username'] = xarML('That username is already taken.');

                } else {
                    // check for disallowed usernames
                    $disallowednames = xarModGetVar('registration','disallowednames');
                    if (!empty($disallowednames)) {
                        $disallowednames = unserialize($disallowednames);
                        $disallowednames = explode("\r\n", $disallowednames);
                        if (in_array ($username, $disallowednames)) {
                            $invalid['username'] = xarML('That username is either reserved or not allowed on this website');
                        }
                    }
                }
            }

            // check if the real name is empty
            if (empty($realname)){
                $invalid['realname'] = xarML('You must provide your display name to continue.');

            } else {
                // TODO: add some other limitations ?
            }

            // check if the email is empty
            if (empty($email)){
                $invalid['email'] = xarML('You must provide a valid email address to continue.');
            } else {

                $emailcheck = xarModAPIFunc('registration',
                                            'user',
                                            'validatevar',
                                            array('var' => $email,
                                                  'type' => 'email'));

                if ($emailcheck == false) {
                    $invalid['email'] = xarML('There is an error in your email address');
                }

                if(xarModGetVar('registration','uniqueemail')) {
                    // check for duplicate email address
                    $user = xarModAPIFunc('roles', 'user', 'get',
                                   array('email' => $email));
                    if ($user != false) {
                        unset($user);
                        $invalid['email'] = xarML('That email address is already registered.');
                    }

                }

                // check for disallowed email addresses
                $disallowedemails = xarModGetVar('registration','disallowedemails');
                if (!empty($disallowedemails)) {
                    $disallowedemails = unserialize($disallowedemails);
                    $disallowedemails = explode("\r\n", $disallowedemails);
                    if (in_array ($email, $disallowedemails)) {
                        $invalid['email'] = xarML('That email address is either reserved or not allowed on this website');
                    }

                }
            }

            if (empty($agreetoterms)){
                $invalid['agreetoterms'] = xarML('You must agree to the terms and conditions of this website to register an account.');
            }

            // Check password and set
            if (xarModGetVar('registration', 'chooseownpassword')) {
                $minpasslength = xarModGetVar('registration', 'minpasslength');
                if (strlen($pass2) < $minpasslength) {
                    $invalid['pass1'] = xarML('Your password must be #(1) characters long.', $minpasslength);
                    $invalid['pass2'] = xarML('Your password must be #(1) characters long.', $minpasslength);
                }

                if ((empty($pass1)) || (empty($pass2))) {
                    $invalid['pass2'] = xarML('You must enter the same password twice');
                } elseif ($pass1 != $pass2) {
                    $invalid['pass2'] = xarML('The passwords do not match');
                } else {
                    $pass = $pass1;
                }
            }
            if (empty($pass)){
                $pass = '';
            }
            $checkdynamic = xarModGetVar('registration', 'showdynamic');
            if ($checkdynamic){
                // dynamic properties (if any)
                $properties = null;
                $isvalid = true;
                if (xarModIsAvailable('dynamicdata')) {
                    // get the Dynamic Object defined for this module (and itemtype, if relevant)
                    $object = xarModAPIFunc('dynamicdata','user','getobject',
                                              array('module' => 'roles'));
                    if (isset($object) && !empty($object->objectid)) {

                        // check the input values for this object !
                        $isvalid = $object->checkInput();

                        // get the Dynamic Properties of this object
                        $properties =& $object->getProperties();
                    }
                }
            } else {
                $properties = array();
                $isvalid = true;
            }

            // new authorisation code
            $authid = xarSecGenAuthKey();

            // check if any of the fields (or dynamic properties) were invalid
            if (count($invalid) > 0 || !$isvalid) {
                // if so, return to the previous template
                return xarTplModule('registration','user', 'registerform', array('authid'      => $authid,
                                                                                 'values'      => $values,
                                                                                 'invalid'     => $invalid,
                                                                                 'properties'  => $properties,
                                                                                 'hookoutput'  => $hookoutput,
                                                                                 'createlabel' => xarML('Create Account'),
                                                                                 'userlabel'   => xarML('New User')));
            }

            // everything seems OK -> go on to the next step
            $data = xarTplModule('registration','user', 'confirmregistration', array('username'    => $username,
                                                                                     'email'       => $email,
                                                                                     'realname'    => $realname,
                                                                                     'pass'        => $pass,
                                                                                     'ip'          => $ip,
                                                                                     'authid'      => $authid,
                                                                                     'properties'  => $properties,
                                                                                     'hookoutput'  => $hookoutput,
                                                                                     'createlabel' => xarML('Create Account')));

            break;
        case 'createuser':
            if (!xarVarFetch('username','str:1:100',$username,'',XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('realname','str:1:100',$realname,'',XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('pass','str:4:100',$pass,'',XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('ip','str:4:100',$ip,'',XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('email','str:1:100',$email,'',XARVAR_NOT_REQUIRED)) return;

            //Set some general vars that we need in various options
            $pending = xarModGetVar('registration', 'explicitapproval');
            $authmoduleid=(int)xarModGetVar('roles','defaultauthmodule');
            if (isset($authmoduleid)) {
               $authmodule=xarModGetNameFromID($authmoduleid);
            }else {
                //fallback to? Use our known auth module for now
               $authmodule='authsystem';
            }
            $loginlink =xarModURL($authmodule,'user','main');

            $tplvars=array();
            $tplvars['loginlink']=$loginlink;
            $tplvars['pending']=$pending;

            // Confirm authorisation code.
            if (!xarSecConfirmAuthKey()) return;
            if (empty($pass)){
                $pass = xarModAPIFunc('roles',
                                      'user',
                                      'makepass');
            }
            // Create confirmation code and time registered
            $confcode = xarModAPIFunc('roles',
                                      'user',
                                      'makepass');
            $now = time();

            $requireValidation = xarModGetVar('registration', 'requirevalidation');

            if ($requireValidation == false) {

                $pending = xarModGetVar('registration', 'explicitapproval');

                if ($pending == 1) $state = ROLES_STATE_PENDING;
                else $state = ROLES_STATE_ACTIVE;
                $userdata = array('uname'  => $username,
                                'realname' => $realname,
                                'email'    => $email,
                                'pass'     => $pass,
                                'date'     => $now,
                                'valcode'  => $confcode,
                                'state'    => $state);

                $uid = xarModAPIFunc('roles', 'admin', 'create', $userdata);

                if ($uid == 0) return;

                /* Call hooks in here
                 * This might be double as the roles hook will also call the create,
                 * but the new hook wasn't called there, so no data is passed
                 */
                $userdata['module'] = 'registration';
                $userdata['itemid'] = $uid;
                xarModCallHooks('item', 'create', $uid, $userdata);

                // Send an e-mail to the admin if notification is required, 
                // what? just those that don't need to validate ...

                if (xarModGetVar('registration', 'sendnotice')) {

                    if (xarModGetVar('registration', 'showterms') == 1) {
                        // User has agreed to the terms and conditions.
                        $terms= '';
                        $terms = xarML('This user has agreed to the site terms and conditions.');
                    }

                    $emailargs = array('adminname'    => xarModGetVar('mail', 'adminname'),
                                       'adminemail'   => xarModGetVar('registration', 'notifyemail'),
                                       'userrealname' => $realname,
                                       'username'     => $username,
                                       'useremail'    => $email,
                                       'terms'        => $terms,
                                       'uid'          => $uid,
                                       'userstatus'   => $state
                                       );

                    if (!xarModAPIFunc('registration', 'user', 'notifyadmin', $emailargs)) {
                       return; // TODO ...something here if the email is not sent..
                    }
                }

                //Insert the user into the default users group
                $userRole = xarModGetVar('roles', 'defaultgroup');

                 // Get the group id
                $defaultRole = xarModAPIFunc('roles', 'user', 'get', array('name'  => $userRole,'type' => 1));

                if (empty($defaultRole)) return;
                // Make the user a member of the users role
                if(!xarMakeRoleMemberByID($uid, $defaultRole['uid'])) return;
                xarModSetVar('roles', 'lastuser', $uid);

                if ($pending == 1) $data = xarTplModule('roles','user', 'getvalidation', $tplvars);
                else {
                     //send welcome email (option)
                    if (xarModGetVar('registration', 'sendwelcomeemail')) {
                        if (!xarModAPIFunc('roles',  'admin', 'senduseremail',
                                             array('uid' => array($uid => '1'),
                                                   'mailtype'    => 'welcome'))) {
                            $msg = xarML('Problem sending welcome email');
                            xarErrorSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
                        }
                    }
                    xarModAPIFunc('authsystem', 'user', 'login',
                                   array('uname' => $username,
                                         'pass' => $pass,
                                         'rememberme' => 0));
                    $redirect=xarServerGetBaseURL();
                    xarResponseRedirect($redirect);
                }
            } else {
                $userdata = array('uname'    => $username,
                                    'realname' => $realname,
                                    'email'    => $email,
                                    'pass'     => $pass,
                                    'date'     => $now,
                                    'valcode'  => $confcode,
                                    'state'    => ROLES_STATE_NOTVALIDATED);
                // Create user - this will also create the dynamic properties (if any) via the create hook
                $uid = xarModAPIFunc('roles', 'admin', 'create', $userdata );

                // Check for user creation failure
                if ($uid == 0) return;

                /* Call hooks in here for the moment */
                $userdata['module'] = 'registration';
                $userdata['itemid'] = $uid;
                xarModCallHooks('item', 'create', $uid, $userdata);

                //Insert the user into the default users role
                $userRole = xarModGetVar('roles', 'defaultgroup');

                // Get the group id
                $defaultRole = xarModAPIFunc('roles', 'user', 'get',
                                              array('name'  => $userRole,
                                                    'type'   => 1));

                if (empty($defaultRole)) return;

                // Make the user a member of the users role
                if(!xarMakeRoleMemberByID($uid, $defaultRole['uid'])) return;

                // TODO: make sending mail configurable too, depending on the other options ?
                if (!xarModAPIFunc('roles', 'admin', 'senduseremail',
                                    array('uid'      => array($uid => '1'),
                                          'mailtype' => 'confirmation',
                                          'ip'       => $ip,
                                          'pass'     => $pass))) {
                    $msg = xarML('Problem sending confirmation email');
                    xarErrorSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
                }

                $data = xarTplModule('registration','user', 'waitingconfirm');
            }
            break;
    }
    return $data;
}

?>

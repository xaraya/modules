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
 * @param string phase The phase we are in. Each phase has an extra set of params
                        choices
                        checkage
                        registerform (DEFAULT)
                        checkregistration
                        createuser
 * @return array
 * @TODO jojodee - rethink and provide cleaner separation between roles, authsystem/authentication and registration
 */
function registration_user_register()
{
    // Security check
    if (!xarSecurityCheck('ViewRegistration')) return;

    //If a user is already logged in, no reason to see this.
    //We are going to send them to their account.
    if (xarUserIsLoggedIn()) {
        xarResponseRedirect(xarModURL('registration', 'user', 'terms'));
       return true;
    }
    $allowregistration = xarModVars::get('registration', 'allowregistration');
    if ($allowregistration != true) {
        $msg = xarML('Registration has been suspended');
        xarErrorSet(XAR_USER_EXCEPTION, 'NO_PERMISSION', new DefaultUserException($msg));
        return;
    }

    //we could turn of registration, but let's check for site lock . We don't want people  registering during this period
     $lockvars = unserialize(xarModVars::get('roles','lockdata'));
     if ($lockvars['locked'] ==1) {
        xarErrorSet(XAR_SYSTEM_MESSAGE,
       'SITE_LOCKED',
        new SystemMessage($lockvars['message']));
        return;
     }

    xarTplSetPageTitle(xarML('New Account'));
    if (!xarVarFetch('phase','str:1:100',$phase,'request',XARVAR_NOT_REQUIRED)) return;


    switch(strtolower($phase)) {

        case 'choices':
            xarTplSetPageTitle(xarML('Log In'));
            $loginlabel = xarML('Sign In');
            $data       = xarTplModule('authsystem','user', 'choices', array('loginlabel' => $loginlabel));
            break;

        case 'checkage':
            $minage     = xarModVars::get('registration', 'minage');
            $submitlink = xarModURL('registration', 'user', 'register',array('phase' => 'registerform'));
            $data       = xarTplModule('registration','user', 'checkage',
                                 array('minage'     => $minage,
                                       'submitlink' => $submitlink));
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
            $item['phase']  = $phase;
            $hooks = xarModCallHooks('item', 'new', '', $item);

            if (empty($hooks)) {
                $hookoutput = array();
            } else {
                /* You can use the output from individual hooks in your template too, e.g. with
                 * $hookoutput['categories'], $hookoutput['dynamicdata'], $hookoutput['keywords'] etc.
                 */
                $hookoutput = $hooks;
            }

            $data = xarTplModule('registration','user', 'registerform',
                           array('authid'     => $authid,
                                 'values'     => $values,
                                 'invalid'    => $invalid,
                                 'properties' => $properties,
                                 'hookoutput' => $hookoutput,
                                 'withupload' => isset($withupload) ? $withupload : (int) FALSE,
                                 'userlabel'  => xarML('New User')));
            break;

        case 'checkregistration':

            if (!xarVarFetch('username',     'str:1:100', $username,     '',    XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('realname',     'str:1:100', $realname,     '',    XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('pass1',        'str:4:100', $pass1,        '',    XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('pass2',        'str:4:100', $pass2,        '',    XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('email',        'str:1:100', $email,        '',    XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('agreetoterms', 'checkbox',  $agreetoterms, false, XARVAR_NOT_REQUIRED)) return;

            // Confirm authorisation code.
            if (!xarSecConfirmAuthKey()) return;

            $ip = xarServerGetVar('REMOTE_ADDR');
            $invalid = xarModApiFunc('registration','user','checkvar', array('type'=>'ip', 'var'=>$ip));
            if (!empty($invalid)) {
                xarErrorSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($invalid));
                return;
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
            $item['itemid'] = '';
            $item['values'] = $values; // TODO: this includes the password. Do we want this?
            $item['phase']  = $phase;
            $hooks = xarModCallHooks('item', 'new','', $item);

            if (empty($hooks)) {
                $hookoutput = array();
            } else {
                 $hookoutput = $hooks;
            }

            // invalid fields (we'll check this below)
            $invalid = array();

            // check username
            $invalid['username'] = xarModApiFunc('registration','user','checkvar', array('type'=>'username', 'var'=>$username));

            // check real name
            $invalid['realname'] = xarModApiFunc('registration','user','checkvar', array('type'=>'realname', 'var'=>$realname));

            // check email
            $invalid['email'] = xarModApiFunc('registration','user','checkvar', array('type'=>'email', 'var'=>$email));

            // agree to terms (kind of dumb, but for completeness)
            $invalid['agreetoterms'] = xarModApiFunc('registration','user','checkvar', array('type'=>'agreetoterms', 'var'=>$agreetoterms));

            // Check password and set
            $pass = '';
            if (xarModVars::get('registration', 'chooseownpassword')) {
                $invalid['pass1'] = xarModApiFunc('registration','user','checkvar', array('type'=>'pass1', 'var'=>$pass1 ));
                if (empty($invalid['pass1'])) {
                    $invalid['pass2'] = xarModApiFunc('registration','user','checkvar', array('type'=>'pass2', 'var'=>array($pass1,$pass2) ));
                }
                if (empty($invalid['pass1']) && empty($invalid['pass2']))   {
                    $pass = $pass1;
                }
            }

            // dynamic properties
            $checkdynamic = xarModVars::get('registration', 'showdynamic');
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
            $a = array_count_values($invalid); // $a[''] will be the count of null values
            $countInvalid = count($invalid) - $a[''];
            if ($countInvalid > 0 || !$isvalid) {
                // if so, return to the previous template
                return xarTplModule('registration','user', 'registerform',
                                 array('authid'      => $authid,
                                       'values'      => $values,
                                       'invalid'     => $invalid,
                                       'properties'  => $properties,
                                       'hookoutput'  => $hookoutput,
                                       'createlabel' => xarML('Create Account'),
                                       'userlabel'   => xarML('New User')));
            }

            // everything seems OK -> go on to the next step
            $data = xarTplModule('registration','user', 'confirmregistration',
                                 array('username'    => $username,
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
            if (!xarVarFetch('username',  'str:1:100', $username, '', XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('realname',  'str:1:100', $realname, '', XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('pass',      'str:4:100', $pass,     '', XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('ip',        'str:4:100', $ip,       '', XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('email',     'str:1:100', $email,    '', XARVAR_NOT_REQUIRED)) return;

            //Set some general vars that we need in various registration options
            $pending = xarModVars::get('registration', 'explicitapproval'); //Require admin approval for account
            $requireValidation = xarModVars::get('registration', 'requirevalidation'); //require user validation of account by email

            //Get the default auth module data
            //this 'authmodule' was introduced previously (1.1 merge ?)
            // - the column in roles re default auth module that this apparently used to refer to is redundant
            $defaultauthdata     = xarModAPIFunc('roles', 'user', 'getdefaultauthdata');
            $defaultloginmodname = $defaultauthdata['defaultloginmodname'];
            $authmodule          = $defaultauthdata['defaultauthmodname'];

            //jojo - should just use authsystem now as we used to pre 1.1 merge
            $loginlink =xarModURL($defaultloginmodname,'user','main');

            //variables required for display of correct validation template to users, depending on registration options
            $tplvars = array();
            $tplvars['loginlink'] = $loginlink;
            $tplvars['pending']   = $pending;


            // Confirm authorisation code.
            if (!xarSecConfirmAuthKey()) return;

            // determine state of this create user
            $state = xarModAPIFunc('registration','user','createstate' );

            // need a password
            if (empty($pass)){
                $pass = xarModAPIFunc('roles', 'user', 'makepass');
            }

            // Create confirmation code if required
            if ($requireValidation) {
                $confcode = xarModAPIFunc('roles', 'user', 'makepass');
            } else {
                $confcode ='';
            }

            //Create the user
            $userdata = array('uname'  => $username,
                              'realname' => $realname,
                    //FIXME...
                    'itemtype' => 2,
                              'email'    => $email,
                              'pass'     => $pass,
                              'date'     => time(),
                              'valcode'  => $confcode,
                              'parentid' => xarModVars::get('roles', 'defaultgroup'),
                              'state'    => $state);

            $uid = xarModAPIFunc('roles', 'admin', 'create', $userdata);
            if (empty($uid)) return;
            xarModVars::set('roles', 'lastuser', $uid);

            //Make sure the user email setting is off unless the user sets it
            xarModSetUserVar('roles','usersendemails', false, $uid);

            /* Call hooks in here
             * This might be double as the roles hook will also call the create,
             * but the new hook wasn't called there, so no data is passed
             */
             $userdata['module'] = 'registration';
             $userdata['itemid'] = $uid;
             xarModCallHooks('item', 'create', $uid, $userdata);

             // Option: If admin requires notification of a new user, and no validation required,
             // send out an email to Admin



            // Let's finish by sending emails to those that require it based on options - the user or the admin
            // and redirecting to appropriate pages that depend on user state and options set in the registration config
            // note: dont email password if user chose his own (should this condition be in the createnotify api instead?)
            $ret = xarModApiFunc('registration','user','createnotify',
                array(  'username'  => $username,
                        'realname'  => $realname,
                        'email'     => $email,
                        'pass'      => (xarModVars::get('registration', 'chooseownpassword')) ? '' : $pass,
                        'uid'       => $uid,
                        'ip'        => $ip,
                        'state'     => $state));
            if (!$ret) return;

            // go to appropriate page, based on state
            if ($state == ROLES_STATE_ACTIVE) {
                // log in and redirect
                xarModAPIFunc('authsystem', 'user', 'login',
                    array(  'uname'      => $username,
                            'pass'       => $pass,
                            'rememberme' => 0));
                $redirect=xarServerGetBaseURL();
                xarResponseRedirect($redirect);

            } else if ($state == ROLES_STATE_PENDING) {
                // If we are still waiting on admin to review pending accounts send the user to a page to notify them
                // This page is for options of validation alone, validation and pending, and pending alone
                $data = xarTplModule('roles','user', 'getvalidation', $tplvars);

            } else { // $state == ROLES_STATE_NOTVALIDATED
                $data = xarTplModule('registration','user', 'waitingconfirm');
            }

            break;
    }
    return $data;
}
?>
<?php
/**
 * @package modules
 * @copyright (C) copyright-placeholder
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage registration
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
 */
sys::import('modules.dynamicdata.class.objects.master');

function registration_user_register()
{
    if (!xarSecurityCheck('ViewRegistration')) return;

    //If a user is already logged in, no reason to see this.
    //We are going to send them to their account.
    if (xarUserIsLoggedIn()) {
        xarResponseRedirect(xarModURL('roles', 'user', 'account'));
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


    $regobjectname = xarModVars::get('registration', 'registrationobject');
    $authid = xarSecGenAuthKey();

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

        case 'registerformcycle':
            $fieldvalues = xarSession::getVar('Registration.UserInfo');
        case 'registerform': 
        default:

            $object = DataObjectMaster::getObject(array('name' => $regobjectname));
            if(empty($object)) return;

            if (isset($fieldvalues)) {
                $object->setFieldValues($fieldvalues);
            }

            /* Call hooks here, others than just dyn data
             * We pass the phase in here to tell the hook it should check the data
             */
            $item['module'] = 'registration';
            $item['itemid'] = '';
            $item['values'] = $object->getFieldValues();
            $item['phase']  = $phase;
            $hooks = xarModCallHooks('item', 'new', '', $item);

            if (empty($hooks)) {
                $hookoutput = array();
            } else {
                $hookoutput = $hooks;
            }

            $data = xarTplModule('registration','user', 'registerform',
                           array('authid'     => $authid,
                                 'object'    => $object,
                                 'hookoutput' => $hookoutput));
            break;

        case 'checkregistration':
            if (!xarVarFetch('agreetoterms', 'checkbox',  $agreetoterms, false, XARVAR_NOT_REQUIRED)) return;

//            if (!xarSecConfirmAuthKey()) return;

            $ip = xarServerGetVar('REMOTE_ADDR');
            $invalid = xarModApiFunc('registration','user','checkvar', array('type'=>'ip', 'var'=>$ip));
            if (!empty($invalid)) {
                xarErrorSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($invalid));
                return;
            }

            $object = DataObjectMaster::getObject(array('name' => $regobjectname));
            $isvalid = $object->checkInput();

            /* Call hooks here, others than just dyn data
             * We pass the phase in here to tell the hook it should check the data
             */
            $item['module'] = 'registration';
            $item['itemid'] = '';
            $item['values'] = $object->getFieldValues(); // TODO: this includes the password. Do we want this?
            $item['phase']  = $phase;
            $hooks = xarModCallHooks('item', 'new','', $item);

            if (empty($hooks)) {
                $hookoutput = array();
            } else {
                 $hookoutput = $hooks;
            }

            if (!$isvalid || !$agreetoterms) {
                $data = array('authid'     => $authid,
                              'object'     => $object,
                              'hookoutput' => $hookoutput);
                if (!$agreetoterms) $data['termsmsg'] = true;
                return xarTplModule('registration','user', 'registerform',$data);
            }
            // invalid fields (we'll check this below)
            $invalid = array();

            $values = $object->getFieldValues();
            if (xarModVars::get('roles','uniqueemail')) {
                $user = xarModAPIFunc('roles','user', 'get', array('email' => $email));
                if ($user) throw new DuplicateException(array('email',$email));
            }

            // agree to terms (kind of dumb, but for completeness)
            $invalid['agreetoterms'] = xarModApiFunc('registration','user','checkvar', array('type'=>'agreetoterms', 'var'=>$agreetoterms));

            // Check password and set
            // @todo find a better way to turn choose own password on and off that works nicely with dd objects
            //$pass = '';
            if (xarModVars::get('registration', 'chooseownpassword')) {
                /*$invalid['pass1'] = xarModApiFunc('registration','user','checkvar', array('type'=>'pass1', 'var'=>$pass1 ));
                if (empty($invalid['pass1'])) {
                    $invalid['pass2'] = xarModApiFunc('registration','user','checkvar', array('type'=>'pass2', 'var'=>array($pass1,$pass2) ));
                }
                if (empty($invalid['pass1']) && empty($invalid['pass2']))   {
                    $pass = $pass1;
                }*/
            }

            $count = 0;
            foreach ($invalid as $k => $v) if (!empty($v)) $count + 1;
            // @todo add preview?
            if (!$isvalid || ($count > 0)) {
                $data = array();
                $data['authid'] = $authid;
                $data['object'] = & $object;
                $data['invalid'] = $invalid;
                //$data['preview'] = $preview;
                $item = array();
                $item['module'] = 'registration';
                $item['phase'] = $phase;
                $data['hookoutput'] = xarModCallHooks('item','new','',$item);
                return xarTplModule('registration','user','registerform', $data);
            }

            xarSession::setVar('Registration.UserInfo',$values);
            // everything seems OK -> go on to the next step
            $data = xarTplModule('registration','user', 'confirmregistration',
                                 array('object'      => $object,
                                       'authid'      => $authid,
                                       'hookoutput'  => $hookoutput));

            break;

        case 'createuser':
        
            // Branch off to payment here if required
            $module = xarMod::getRegID('registration');
            if (xarModIsAvailable('payments') && xarModItemVars::get('payments','payments_active', $module)) {
                $object = DataObjectMaster::getObject(array('name' => xarModItemVars::get('payments', 'orderobject', $module)));
                $data['properties'] = $object->getProperties();
                $data['return_url']['cancel_return'] = xarModURL('registration','user','register',array('phase' => 'checkregistration'));
                $data['return_url']['cancel_text'] = xarML("Click to return to the registration page");
                $data['return_url']['success_return'] = xarML("User Creation");
                $data['return_url']['success_return_link'] = xarModURL('registration', 'user', 'register', array('phase' => 'confirmcreateuser'));

                // Save the return URLs for when we come back from the gateway
                xarSession::setVar('return_url',serialize($data['return_url']));

                $data['allowEdit_Payment'] = false;
                $data['authid'] = xarSecGenAuthKey();
    
                $process = xarModItemVars::get('payments','process',$module);
                switch ($process) {
                    case 0:
                    default:
                        $data['layout'] = 'no_process';
                        return xarTplModule('payments','user','errors',$data);
                    case 1:
                        return xarTplModule('payments','user','amount',$data);
                    case 2:
                        return xarTplModule('payments','user','amount',$data);
                    case 3:
                        return xarTplModule('payments','user','amount',$data);
                }
            } else {
                // If we don't branch off to payments do the check
                if (!xarSecConfirmAuthKey()) return;
            }
            
        case 'confirmcreateuser':
        
            $fieldvalues = xarSessionGetVar('Registration.UserInfo');

            $object = DataObjectMaster::getObject(array('name' => $regobjectname));
            if(empty($object)) return;

            // Do we need admin activation of the account?
            if (xarModVars::get('registration', 'explicitapproval')) {
                $fieldvalues['state'] = xarRoles::ROLES_STATE_PENDING;
            }

            //Get the default auth module data
            //this 'authmodule' was introduced previously (1.1 merge ?)
            // - the column in roles re default auth module that this apparently used to refer to is redundant
            $defaultauthdata     = xarModAPIFunc('roles', 'user', 'getdefaultauthdata');
            $defaultloginmodname = $defaultauthdata['defaultloginmodname'];
            $authmodule          = $defaultauthdata['defaultauthmodname'];

            //jojo - should just use authsystem now as we used to pre 1.1 merge
            $loginlink = xarModURL($defaultloginmodname,'user','main');

            //variables required for display of correct validation template to users, depending on registration options
            $data['loginlink'] = $loginlink;
            $data['pending']   = xarModVars::get('registration', 'explicitapproval');

            // Do we require user validation of account by email?
            if (xarModVars::get('registration', 'requirevalidation')) {
                $fieldvalues['state'] = xarRoles::ROLES_STATE_NOTVALIDATED;

                // Create confirmation code
                $confcode = xarModAPIFunc('roles', 'user', 'makepass');
            } else {
                $confcode = '';
            }

            // Update the field values and create the user
            $object->setFieldValues($fieldvalues,1);

            // Create a password and add it if the user can't create one himself
            if (!xarModVars::get('registration', 'chooseownpassword')){
                $pass = xarModAPIFunc('roles', 'user', 'makepass');
                $fieldvalues['password'] = $pass;
                $object->setFieldValues($fieldvalues);
            }

            // Create the user, assigning it to a parent
            $id = $object->createItem(array('parentid' => xarModVars::get('registration','defaultgroup')));

            if (empty($id)) return;
            xarModVars::set('roles', 'lastuser', $id);

            //Make sure the user email setting is off unless the user sets it
            xarModUserVars::set('roles','allowemail', false, $id);

            $hookdata = $fieldvalues;
            $hookdata['itemtype'] = xarRoles::ROLES_USERTYPE;
            $hookdata['module'] = 'registration';
            $hookdata['itemid'] = $id;
            xarModCallHooks('item', 'create', $id, $hookdata);

            // We allow "state" or "roles_state"
            if (!isset($fieldvalues['state'])) {
                if (isset($fieldvalues['roles_state'])) {
                    $fieldvalues['state'] = $fieldvalues['roles_state'];
                } else {
                    throw new Exception("Missing a 'state' property for the registration data");
                }
            } 
            
            // go to appropriate page, based on state
            if ($fieldvalues['state'] == xarRoles::ROLES_STATE_ACTIVE) {
                // log in and redirect

                /* Need a more general definition of what it means to "log in"
                xarModAPIFunc('authsystem', 'user', 'login',
                        array('uname'      => $uname,
                              'pass'       => $pass,
                              'rememberme' => 0));
                */
                $data = xarTplModule('registration','user', 'accountstate', array('state' => $fieldvalues['state']));

            } else if ($fieldvalues['state'] == xarRoles::ROLES_STATE_PENDING) {
                // If we are still waiting on admin to review pending accounts send the user to a page to notify them
                // This page is for options of validation alone, validation and pending, and pending alone
                $data = xarTplModule('roles','user', 'getvalidation', $data);

            } else { // $state == xarRoles::ROLES_STATE_NOTVALIDATED
                $data = xarTplModule('registration','user', 'waitingconfirm');
            }

            break;
    }
    return $data;
}
?>
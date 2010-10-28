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

	//xarSession::delVar('Registration.UserInfo');

    //If a user is already logged in, no reason to see this.
    //We are going to send them to their account.
    if (xarUserIsLoggedIn()) {
        xarResponse::redirect(xarModURL('roles', 'user', 'account'));
       return true;
    }
    $allowregistration = xarModVars::get('registration', 'allowregistration');
    if ($allowregistration != true) {
        $msg = xarML('Registration has been suspended');
        return xarResponse::Forbidden($msg);
    }

    // Check for site lock - we don't want people registering during this period
    $lockvars = unserialize(xarModVars::get('roles','lockdata'));
    if ($lockvars['locked'] ==1) {
        return xarResponse::Forbidden($lockvars['message']);
    }
    
    if (!xarVarFetch('phase', 'pre:trim:lower:enum:registerform:registerformcycle:checkregistration:createuser', $phase, 'registerform', XARVAR_NOT_REQUIRED)) return;
    
    $regobjectname = xarModVars::get('registration', 'registrationobject');

    switch ($phase) {
        case 'registerformcycle':
            $fieldvalues = xarSession::getVar('Registration.UserInfo');
        case 'registerform': // display form to user 
        default:
        
            // Check for disallowed IP here, pointless waiting until the user has attempted to register
            $ip = xarServer::getVar('REMOTE_ADDR');
            $invalid = xarMod::apiFunc('registration','user','checkvar', array('type'=>'ip', 'var'=>$ip));
            if (!empty($invalid)) {
                return xarResponse::Forbidden($invalid);
            }  

            // see if we have a minimum age requirement...
            // NOTE: this is done here instead of in the checkage phase used previously
            // to prevent user bypass by going directly to the registerform phase 
            $minage = xarModVars::get('registration', 'minage');
            // if minage is empty we can skip this check entirely            
            if (empty($minage)) {
                $ageconfirm = true;
            } else {
                // see if user confirmed agecheck by following the submitlink 
                if (!xarVarFetch('ageconfirm', 'checkbox', $ageconfirm, false, XARVAR_NOT_REQUIRED))return;
                // see if agecheck was confirmed previously (if we're in the form cycle) 
                if (empty($ageconfirm))
                    $ageconfirm = xarSession::getVar('registration.ageconfirm');
            } 
            // unconfirmed, present the confirmation template to user 
            if (!$ageconfirm) {
                $tpldata = array(
                    'submitlink' => xarModURL('registration', 'user', 'register', 
                                        array('phase' => 'registerform', 'ageconfirm' => 1)),
                    'minage' => $minage,
                );
                return xarTplModule('registration', 'user', 'checkage', $tpldata);
            }
            // age confirmed, set confirmation to session variable (for form cycle)
            xarSession::setVar('registration.ageconfirm', $ageconfirm);
            
            // initialise registration object
            $object = DataObjectMaster::getObject(array('name' => $regobjectname));
            if (empty($object)) return;

            if (isset($fieldvalues)) 
                $object->setFieldValues($fieldvalues);
            
            $item = array();          
            $item['module'] = 'registration';
            $item['itemid'] = '';
            $item['itemtype'] = xarRoles::ROLES_USERTYPE;
            // CHECKME: hooks don't normally need these, are they specific to a particular hook?
            $item['values'] = $object->getFieldValues();
            $item['phase']  = $phase;
            $hooks = xarModCallHooks('item', 'new', '', $item);
            
            $data = array();
            $data['object'] = $object;            
            $data['hookoutput'] = !empty($hooks) ? $hooks : '';
            $data['authid'] = xarSecGenAuthKey();
            
            xarTplSetPageTitle(xarML('New Account'));
            return xarTplModule('registration', 'user', 'registerform', $data);              
            
        break;
        
        case 'checkregistration': // validate input and ask for account create confirmation
        
            // this prevents users passing input via get params and by-passing age/ip check
            if (!xarSession::getVar('registration.ageconfirm'))
                xarResponse::redirect(xarModURL('registration', 'user', 'register'));

            // initialise registration object
            $object = DataObjectMaster::getObject(array('name' => $regobjectname));
            if (empty($object)) return;            
            // Check object input            
            $isvalid = $object->checkInput();
            
            $invalid = array();
            // Check terms agreement
            if (!xarVarFetch('agreetoterms', 'checkbox', $agreetoterms, false, XARVAR_NOT_REQUIRED)) return;
            if (!$agreetoterms)
                $invalid['agreetoterms'] = xarMod::apiFunc('registration','user','checkvar', 
                    array('type'=>'agreetoterms', 'var'=>$agreetoterms));

            // check unique email if necessary 
            if (xarModVars::get('roles','uniqueemail')) {
                $email = $object->properties['email']->value;
                $user = xarMod::apiFunc('roles','user', 'get', array('email' => $email));
                //if ($user) throw new DuplicateException(array('email',$email));
                if ($user) {
                    $isvalid = false;
                    $object->properties['email']->invalid = xarML('This email address is already registered');
                }
            }

            $item = array();          
            $item['module'] = 'registration';
            $item['itemid'] = '';
            $item['itemtype'] = xarRoles::ROLES_USERTYPE;
            // CHECKME: hooks don't normally need these, are they specific to a particular hook?
            $item['values'] = $object->getFieldValues();
            $item['phase']  = $phase;
            $hooks = xarModCallHooks('item', 'new', '', $item);

            if (!$isvalid || !empty($invalid)) {
                $data = array();
                $data['object'] = $object;            
                $data['hookoutput'] = !empty($hooks) ? $hooks : '';
                $data['authid'] = xarSecGenAuthKey();
                $data['invalid'] = $invalid;
                return xarTplModule('registration','user','registerform', $data);
            }
            
            // Set values to session for form cycle and create user phases
            xarSession::setVar('Registration.UserInfo',$object->getFieldValues());
            
            $data = array();
            $data['object'] = $object;
            $data['authid'] = xarSecGenAuthKey();
            $data['hookoutput'] = !empty($hooks) ? $hooks : '';
            
            return xarTplModule('registration','user', 'confirmregistration', $data);

        break;
        
        case 'createuser': // create account 

		var_dump(xarSession::getVar('Registration.UserInfo'));
		exit;

        /* commenting this out for now since payments doesn't exist in the repo's
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
        */
        
            if (!xarSecConfirmAuthKey()) 
                return xarTplModule('privileges', 'user', 'errors', array('layout' => 'bad_author'));

            $fieldvalues = xarSession::getVar('Registration.UserInfo');

            $object = DataObjectMaster::getObject(array('name' => $regobjectname));
            if(empty($object)) return;

            // Do we need admin activation of the account?
            if (xarModVars::get('registration', 'explicitapproval')) {
                $fieldvalues['state'] = xarRoles::ROLES_STATE_PENDING;
            }

            //Get the default auth module data
            //this 'authmodule' was introduced previously (1.1 merge ?)
            // - the column in roles re default auth module that this apparently used to refer to is redundant
            $defaultauthdata     = xarMod::apiFunc('roles', 'user', 'getdefaultauthdata');
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
                $confcode = xarMod::apiFunc('roles', 'user', 'makepass');
                $fieldvalues['validationcode'] = $confcode;            
            } else {
                $confcode = '';
            }
            

            // Update the field values and create the user
            $object->setFieldValues($fieldvalues,1);

            // Create a password and add it if the user can't create one himself
            if (!xarModVars::get('registration', 'chooseownpassword')){
                $pass = xarMod::apiFunc('roles', 'user', 'makepass');
                $fieldvalues['password'] = $pass;
                $object->setFieldValues($fieldvalues);
            }

            // Create the user, assigning it to a parent
            $id = $object->createItem(array('parentid' => xarModVars::get('registration','defaultgroup')));

            if (empty($id)) return;
            xarModVars::set('roles', 'lastuser', $id);

/* Already done in createItem()
            //Make sure the user email setting is off unless the user sets it
            xarModUserVars::set('roles','allowemail', false, $id);

            $hookdata = $fieldvalues;
            $hookdata['itemtype'] = xarRoles::ROLES_USERTYPE;
            $hookdata['module'] = 'registration';
            $hookdata['itemid'] = $id;
            xarModCallHooks('item', 'create', $id, $hookdata);
*/
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
                xarMod::apiFunc('authsystem', 'user', 'login',
                        array('uname'      => $uname,
                              'pass'       => $pass,
                              'rememberme' => 0));
                */
                $data = xarTplModule('registration','user', 'accountstate', 
                    array('state' => $fieldvalues['state']));

            } else if ($fieldvalues['state'] == xarRoles::ROLES_STATE_PENDING) {
                // If we are still waiting on admin to review pending accounts send the user to a page to notify them
                // This page is for options of validation alone, validation and pending, and pending alone
                $data = xarTplModule('roles','user', 'getvalidation', $data);

            } else { // $state == xarRoles::ROLES_STATE_NOTVALIDATED
                $data = xarTplModule('registration','user', 'waitingconfirm');
            }
            // Clean up session vars
			print 'DELETE';
            xarSession::delVar('Registration.UserInfo');
            xarSession::delVar('registration.ageconfirm');
            return $data;
        break;   
    
    }
    
}
?>

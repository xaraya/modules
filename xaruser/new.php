<?php
/**
 * Add a new item
 *
 * @package modules
 * @copyright (C) 2002-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Contact Form Module
 * @link http://xaraya.com/index.php/release/66.html
 * @author potion <ryan@webcommunicate.net>
 */
/**
 * Create a new item of the contactform object
 */
function contactform_user_new($args)
{
	extract($args);

    // See if the current user has the privilege to add an item. We cannot pass any extra arguments here
    if (!xarSecurityCheck('ReadContactForm')) return;

	if(!xarVarFetch('name', 'str', $name, 'contactform_default', XARVAR_NOT_REQUIRED)) {return;}
	if(!xarVarFetch('redirect', 'str', $redirect, NULL, XARVAR_NOT_REQUIRED)) {return;}

	$allowed = xarModVars::get('contactform','contact_objects');
	$allowed = explode(',',$allowed);
	if (!in_array($name, $allowed)) {
		throw new Exception('The object specified is not among your allowed contact objects'); 
		return;
	}

	$template = $name;

	$data['invalid'] = false;

    sys::import('modules.dynamicdata.class.objects.master');
    $data['object'] = DataObjectMaster::getObject(array('name' => $name));
	$config = $data['object']->configuration;

    if (!xarVarFetch('confirm',    'bool',   $data['confirm'], false,     XARVAR_NOT_REQUIRED)) return;

    if ($data['confirm']) {
 
        if (!xarSecConfirmAuthKey()) {
            return xarTplModule('privileges','user','errors',array('layout' => 'bad_author'));
        }        

		/*
		To use recaptcha, download the library...
		http://code.google.com/p/recaptcha/downloads/list?q=label:phplib-Latest
		...and then place the recaptchalib.php file in your web directory.   You also need to enter your private key below and enter your public key in the user-new.xt template.
		
		require_once('recaptchalib.php'); 
		$privatekey = "your_private_key_goes_here";  // replace with your private key
		$resp = recaptcha_check_answer ($privatekey,
									$_SERVER["REMOTE_ADDR"],
									$_POST["recaptcha_challenge_field"],
									$_POST["recaptcha_response_field"]);

		if (!$resp->is_valid) {
		// What happens when the CAPTCHA was entered incorrectly
		//die ("The reCAPTCHA wasn't entered correctly. Go back and try it again." . "(reCAPTCHA said: " . $resp->error . ")");
		die ("The reCAPTCHA wasn't entered correctly. Please go back and try it again.");
		} else {
		  // Your code here to handle a successful verification
		}
		*/
 
        $isvalid = $data['object']->checkInput();
		$invalids = $data['object']->getInvalids();

        if (!empty($invalids)) { 
            $data['invalid'] = $invalids;
			return xarTplModule('contactform','user','new', $data, $template);          
        } else { 

			$vals = $data['object']->getFieldValues();
			foreach ($vals as $key=>$val) {
				${$key} = $val;
			}			

			if (!isset($message)) { 
				throw new Exception('The object is missing a required property.  All contact form objects must have a \'message\' property.'); 
				return; 
			}
		
			if (xarUserIsLoggedIn()) {
				$from_name = xarUserGetVar('uname');
				$from_email = xarUserGetVar('email');
			}

			if (isset($first_name) && isset($last_name)) {
				$from_name = $first_name . ' ' . $last_name;
			}

			if (!isset($from_name)) { 
				throw new Exception('The object is missing a required property.  For anonymous users, all contact form objects must have either a \'from_name\' property or both a \'first_name\' and \'last_name\'.'); 
				return; 
			}

			$save = false;
			if (isset($config['save_to_db'])) {
				if ($config['save_to_db'] == 'true') $save = true;
				if ($config['save_to_db'] == 'false') $save = false;
			} else {
				// if there is no object config, fall back on the module config
				$save = xarModVars::get('contactform','save_to_db');
			}

			if ($save) { 
				if (!$data['object']->createItem()) { 
				throw new Exception('The form submission could not be saved.'); 
				return;
				}
			}
			
			if (isset($ccrecipients)) $ccrecipients = explode(',', $ccrecipients);
			if (isset($bccrecipients)) $bccrecipients = explode(',', $bccrecipients);

			if (!isset($to_email)) $to_email = xarModVars::get('contactform', 'to_email');
			if (!isset($subject)) $subject = xarModVars::get('contactform', 'default_subject');
			if (empty($to_email)) $to_email = xarModVars::get('mail', 'adminmail');
			if (!isset($to_name)) $to_name = xarModVars::get('mail', 'adminname');

			$mailargs['info'] = $to_email;
			$mailargs['name'] = $to_name;
			if (isset($ccrecipients)) $mailargs['ccrecipients'] = $ccrecipients;
			if (isset($bccrecipients)) $mailargs['bccrecipients'] = $bccrecipients;
			if (isset($from_email)) $mailargs['from'] = $from_email;
			$mailargs['fromname'] = $from_name; 

			$vals['subject'] = $subject;
			$subject = xarTplModule('contactform','user','subject', $vals, $template);
			$message = xarTplModule('contactform','user','message', $vals, $template);
			if (xarModVars::get('contactform', 'strip_tags')) {
				$mailargs['subject'] = strip_tags($subject);
				$mailargs['message'] = strip_tags($message);
			} else {
				$mailargs['subject'] = $subject;
				$mailargs['message'] = $message;			
			}
			if (isset($config['usetemplates'])) {
				$mailargs['usetemplates'] = $usetemplates;
			} else {
				$mailargs['usetemplates'] = false;
			}
			
			if (!xarMod::apiFunc('mail','admin','sendmail', $mailargs)) return false;

			if (isset($config['redirect']) && !$redirect) {
				$redirect = $config['redirect'];
			} elseif (!$redirect) {
				$redirect = xarModURL('contactform','user','success');
			}
			xarResponse::Redirect($redirect); 
            return true;
        }
    }

    return xarTplModule('contactform','user','new', $data, $template);
}

?>
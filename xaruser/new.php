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
function contactform_user_new()
{
    // See if the current user has the privilege to add an item. We cannot pass any extra arguments here
    if (!xarSecurityCheck('ReadContactForm')) return;

	if(!xarVarFetch('name', 'str', $name, 'contactform_default', XARVAR_NOT_REQUIRED)) {return;}

	$allowed = xarModVars::get('contactform','contact_objects');
	$allowed = explode(',',$allowed);
	if (!in_array($name, $allowed)) {
		throw new Exception('The object specified is not among your allowed contact objects'); 
		return;
	}

	$template = $name;

	$data['invalid'] = false;

    // Load the DD master object class. This line will likely disappear in future versions
    sys::import('modules.dynamicdata.class.objects.master');
    // Get the object we'll be working with
    $data['object'] = DataObjectMaster::getObject(array('name' => $name));
	$config = $data['object']->configuration;

    // Check if we are in 'preview' mode from the input here - the rest is handled by checkInput()
    // if(!xarVarFetch('preview', 'str', $data['preview'],  NULL, XARVAR_DONT_SET)) {return;}

    // Check if we are submitting the form
    // Here we are testing for a hidden field we define as true on the template, so we can use a boolean (true/false)
    if (!xarVarFetch('confirm',    'bool',   $data['confirm'], false,     XARVAR_NOT_REQUIRED)) return;

    if ($data['confirm']) {
 
        if (!xarSecConfirmAuthKey()) {
            return xarTplModule('privileges','user','errors',array('layout' => 'bad_author'));
        }        
 
        $isvalid = $data['object']->checkInput();
		$invalids = $data['object']->getInvalids();

        if (!empty($invalids)) { 
            $data['invalid'] = $invalids;
			return xarTplModule('contactform','user','new', $data, $template);       
        //} elseif (isset($data['preview'])) { 
			//return xarTplModule('contactform','user','new', $data, $template);        
        } else { 

			$ccrecipients = array();
			$bccrecipients = array();

			$vals = $data['object']->getFieldValues();
			foreach ($vals as $key=>$val) {
				${$key} = $val;
			}

			if (!isset($message)) { 
				throw new Exception('The object is missing a required property.  All contact form objects must have a "message" property.'); 
				return; 
			}
			
			if (isset($config['save_to_db']) && $config['save_to_db'] == 'true') {
				$save = true;
			} else {
				// if there is no object config, fall back on the module config
				$save = xarModVars::get('contactform','save_to_db');
			}
		
			if (xarUserIsLoggedIn()) {
				$from_name = xarUserGetVar('uname');
				$from_email = xarUserGetVar('email');
			}

			if (isset($first_name) && isset($last_name)) {
				$from_name = $first_name . ' ' . $last_name;
			}

			if (!isset($from_name)) { 
				throw new Exception('The object is missing a required property.  For anonymous users, all contact form objects must have either a "from_name" property or both a "first_name" and "last_name".'); 
				return; 
			}

			$item = true;
			if ($save) {
				$item = $data['object']->createItem();
			}

			if (!$save || $item) {

				if (!isset($to_email)) $to_email = xarModVars::get('contactform', 'to_email');
				if (!isset($to_name)) $to_name = xarModVars::get('mail','adminname');
				if (!isset($subject)) $subject = xarModVars::get('contactform', 'default_subject');
	
				$mailargs['info'] = $to_email;
				$mailargs['name'] = $to_name;
				$mailargs['ccrecipients'] = $ccrecipients;
				$mailargs['bccrecipients'] = $bccrecipients;
				$mailargs['subject'] = $subject;
				$mailargs['message'] = $message;
				if (isset($from_email)) $mailargs['from'] = $from_email;
				$mailargs['fromname'] = $from_name; 
				if (!xarMod::apiFunc('mail','admin','sendmail', $mailargs)) return false; 
			} 

            // Jump to the next page
            xarResponse::Redirect(xarModURL('contactform','user','success')); 
            // Always add the next line even if processing never reaches it
            return true;
        }
    }

    return xarTplModule('contactform','user','new', $data, $template);
}

?>
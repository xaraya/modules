<?php
/**
 * Add a new item
 *
 * @package modules
 * @copyright (C) 2002-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage downloads
 * @link http://www.xaraya.com/index.php/release/19741.html
 * @author potion <ryan@webcommunicate.net>
 */
/**
 * Create a new downloads item 
 */
function downloads_admin_new()
{

    if(!xarVarFetch('objectid',       'id',    $objectid,   NULL, XARVAR_DONT_SET)) {return;} 

    $instance = 'All:All:'.xarUserGetVar('id');
    if (!xarSecurityCheck('AddDownloads',0,'Record',$instance)) {
        return;
    }

    $data['filename'] = '';

    // Load the DD master object class. This line will likely disappear in future versions
    sys::import('modules.dynamicdata.class.objects.master');

    $object = DataObjectMaster::getObject(array('name' => 'downloads'));
    $objectid = $object->objectid;
    $data['label'] = $object->label;
    $data['object'] = $object;

    // Check if we are submitting the form
    // Here we are testing for a hidden field we define as true on the template, so we can use a boolean (true/false)
    if (!xarVarFetch('confirm',    'bool',   $data['confirm'], false,     XARVAR_NOT_REQUIRED)) return;

    if ($data['confirm']) {

        // Check for a valid confirmation key. The value is automatically gotten from the template
        if (!xarSecConfirmAuthKey()) {
            return xarTplModule('privileges','user','errors',array('layout' => 'bad_author'));
        }        

        $basepath = xarMod::apiFunc('downloads','admin','getbasepath');
        
        $isvalid = $object->properties['directory']->checkInput();
        $directory = $object->properties['directory']->getValue();
        $object->properties['filename']->initialization_basepath = $basepath;
        $object->properties['filename']->initialization_basedirectory = $directory;
        
        $isvalid = $object->checkinput();

        if (!$isvalid) {
            // Bad data: redisplay the form with the data we picked up and with error messages
            $data['directory'] = $directory;
            $data['filename'] = $object->properties['filename']->getValue();
            return xarTplModule('downloads','admin','new', $data);             
        } else {    
            
            $filename = $object->properties['filename']->getValue();
            
            if (strstr($filename,'.')) {
                $parts = explode('.',$filename);
                $ext = strtolower(end($parts));
            } else {
                $ext = '';
            }

            $instance = 'All:'.$ext.':'.xarUserGetVar('id');
            if (!xarSecurityCheck('SubmitDownloads',0,'Record',$instance)) {
                return;
            }

            $object->properties['filetype']->setValue($ext);

            //$object->properties['directory']->checkInput();
            $directory = $object->properties['directory']->getValue(); 
            
            //$object->properties['description']->checkInput();
            $object->properties['description']->getValue();

            //$object->properties['title']->checkInput();
            $title = $object->properties['title']->getValue();

            //For starters, set the status to submitted
            $object->properties['status']->setValue(0);

            $instance = 'All:'.$ext.':'.xarUserGetVar('id');
            //If we have Add-level privs, set the status to approved
            if (xarSecurityCheck('AddDownloads',0,'Record',$instance)) {
                $object->properties['status']->setValue(2);
            } else {
                //We don't have Add-level privs, so check for auto approval
                $trustedpriv = xarModVars::get('downloads','auto_approve_privilege');
                $trustedpriv = trim($trustedpriv);
                if (!empty($trustedpriv) && xarModAPIFunc('roles','user','checkprivilege', array('id' => xarUserGetVar('id'), 'privilege' => $trustedpriv))) {
                    $object->properties['status']->setValue(2);
                } 
            }

            if (empty($title)) {
                // We want some spaces in titles so the admin view doesn't get distorted
                $filename = str_replace('_',' ',$filename);
                $filename = str_replace('-',' ',$filename);
                $filename = str_replace('.',' ',$filename);
                $object->properties['title']->setValue($filename);
            }

            $itemid = $object->createItem();
            xarResponse::redirect(xarModURL('downloads','admin','modify',array('itemid' =>$itemid))); 
            return true;
        }
    } 

    return $data;
}

?>
<?php
/**
 * Standard main user function
 *
* @package Xaraya
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://xaraya.com
 *
 * @subpackage SiteContact Module
 * @copyright (C) 2004-2008 2skies.com
 * @link http://xarigami.com/project/sitecontact
 * @author Jo Dalle Nogare <icedlava@2skies.com>
 */
/**
 * Main user function
 * This function is the default function, and is called whenever the module is
 * initiated without defining arguments.
 *
 * @author Jo Dalle Nogare
 */
 
function sitecontact_user_main($args)
{
    extract($args);

    $defaultformid=(int)xarModGetVar('sitecontact','defaultform');

    if(!xarVarFetch('company',       'str:1:',  $company,        NULL,    XARVAR_NOT_REQUIRED, XARVAR_PREP_FOR_DISPLAY)) return;
    if(!xarVarFetch('message',       'isset',   $message,        NULL,    XARVAR_DONT_SET)) {return;}
    if(!xarVarFetch('antibotinvalid','int:0:1', $antibotinvalid, NULL,    XARVAR_NOT_REQUIRED)) {return;}
    if(!xarVarFetch('requesttext',   'isset',   $requesttext,    NULL,    XARVAR_NOT_REQUIRED)) {return;}
    if(!xarVarFetch('usermessage',   'isset',   $usermessage,    NULL,    XARVAR_NOT_REQUIRED)) {return;}
    if(!xarVarFetch('sctypename',    'pre:trim:passthru:str:0:', $sctypename, NULL, XARVAR_NOT_REQUIRED)) {return;}
    if(!xarVarFetch('scform',        'str:0:',  $scform,         NULL,    XARVAR_NOT_REQUIRED)) {return;}
    if(!xarVarFetch('botreset',      'bool',    $botreset,       false,   XARVAR_NOT_REQUIRED)) {return;}
    if(!xarVarFetch('scid',          'int:1:',  $scid,           $defaultformid, XARVAR_NOT_REQUIRED)) {return;}
    if (!xarVarFetch('userreferer',  'str:1:',  $userreferer,    '',      XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('casmsg',       'str:1',   $casmsg,         '',      XARVAR_NOT_REQUIRED)) {return;} //formcaptcha
    if (!xarVarFetch('submitted',    'int:0:1', $submitted,      0,       XARVAR_NOT_REQUIRED)) return;

    $formdata=array();

    if (isset($scform) && !empty($scform)) $sctypename = $scform; //provide alternate entry name

    //Have we got a form that is available and active?
    if (isset($sctypename) && trim($sctypename) !='') {
       $formdata = xarModAPIFunc('sitecontact','user','getcontacttypes',array('sctypename'=> $sctypename));
    }elseif (isset($scid) && is_int($scid)) {
       $formdata = xarModAPIFunc('sitecontact','user','getcontacttypes',array('scid' => $scid));
    } else {
        $formdata = xarModAPIFunc('sitecontact','user','getcontacttypes',array('scid' => $defaultformid));
    }

    //Have we got an active form
    if (!is_array($formdata)) { //exists but not active
      //fallback to default form again
      $formdata = xarModAPIFunc('sitecontact','user','getcontacttypes',array('scid' => $defaultformid));
    }
    $formdata=$formdata[0];

    if ($formdata['scactive'] !=1) { //form but not active
        $msg = xarML('The form requested is not available');
        xarErrorSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
        return;
    }

    $scid = $formdata['scid'];
    //now we have a form id - check the user has access
    if (!xarSecurityCheck('ViewSiteContact',0,'ContactForm',"$scid")) {
    $msg = xarML('You do not have permission to access the form you requested.');
        xarErrorSet(XAR_USER_EXCEPTION, 'NO_PRIVILEGES', new DefaultUserException($msg));
        return;
    }
    // Set up defaults returned from any invalid captcha input

    $data['company'] = $company;
    $data['usermessage'] = $usermessage;
    $data['antibotinvalid'] =$antibotinvalid;
    //formcaptcha
    $data['casmsg']= isset($casmsg)?$casmsg:'';
    /*  Generate a onetime authorisation code for this operation */
    $data['authid'] = xarSecGenAuthKey('sitecontact');
    if (!empty($invalid)) {
        $data['invalid']=$invalid;
    }

    $data['submit'] = xarML('Submit');
    //get the options for the form
    if (isset($formdata['soptions'])) {
        $soptions=unserialize($formdata['soptions']);
        if (is_array($soptions)) {
            foreach ($soptions as $k=>$v) {
                $data[trim($k)]=trim($v);
            }
        }
    }

    if (!isset($data['allowbccs']))$data['allowbccs']=0;
    if (!isset($data['allowccs']))$data['allowccs']=0;
    if (!isset($data['allowanoncopy']))$data['allowanoncopy']=0;
    if (!isset($data['useantibot']))$data['useantibot']=false;
    if (!isset($data['savedata']))$data['savedata']=xarModGetVar('sitecontact','savedata')?xarModGetVar('sitecontact','savedata'):0;
    if (!isset($data['permissioncheck']))$data['permissioncheck']=xarModGetVar('sitecontact','permissioncheck');
    if (!isset($data['termslink']))$data['termslink']=xarModGetVar('sitecontact','termslink');
    if (!isset($data['ccrecipients']))$data['ccrecipients']='';
    if (!isset($data['bccrecipients']))$data['bccrecipients']='';
    $customtext   = $formdata['customtext'];
    $customtitle  = $formdata['customtitle'];
    $usehtmlemail = $formdata['usehtmlemail'];
    $allowcopy    = $formdata['allowcopy'];

    $data['customtitle'] = xarVarPrepHTMLDisplay($customtitle);
    $data['customtext']  = xarVarPrepHTMLDisplay($customtext);

    $data['usehtmlemail'] = $usehtmlemail;
    $data['allowcopy'] = $allowcopy;

    $optiontext = $formdata['optiontext'];
    $optionset  = array();
    $selectitem = array();
    $optionset  = explode(',',$optiontext);
    $data['optionset'] = $optionset;
    $optionitems = array();
    foreach ($optionset as $optionitem) {
      $optionitems[] = explode(';',$optionitem);
    }
    $data['optionitems'] = $optionitems;

    if ($botreset== false) { //we don't want to set referer to our own form on an anti-bot return, keep the original referer
        $HTTP_REFERER = xarServerGetVar('HTTP_REFERER');
        if (empty($HTTP_REFERER)) {
            $HTTP_REFERER = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
        }
        $data['userreferer']=$HTTP_REFERER;
        if (isset($data['userreferer']) && !empty($data['userreferer'])) {
            $data['userreferer']=xarVarPrepForDisplay($data['userreferer']);
        }    
    } else {
        $data['userreferer']=$userreferer;
    }

    $setmail='';
    $custitle = isset($customtitle)?xarML($customtitle):xarML('Site Contact');
    xarTplSetPageTitle(xarVarPrepForDisplay($custitle));
    
    $data['requesttext']=isset($requesttext)?$requesttext: '';

    $properties = array();
    $withupload = (int) false;
    if (xarModIsHooked('dynamicdata','sitecontact', $formdata['scid'])) {
            // get the Dynamic Object defined for this module
        $object =  xarModAPIFunc('dynamicdata','user','getobject',
                array('moduleid' =>xarModGetIdFromName('sitecontact'),
                      'itemtype'=>$formdata['scid']));
        if (isset($object) && !empty($object->objectid)) {
            $properties = &$object->getProperties();
        }

        if (is_array($properties)) {
             //get the dd upload value and also name/value pairs
            foreach ($properties as $name => $ddprop) {
                 if (isset($ddprop->upload) && $ddprop->upload == true) {
                     $withupload = (int) true;
                 }
            }
        }
    }

    $data['withupload']=$withupload;

    $webconfirmtext = trim($formdata['webconfirmtext']);
    if (empty($webconfirmtext) || !isset($webconfirmtext)) {

        $webconfirmtext = xarML('Your message has been sent.');
        $webconfirmtext  .='<br />';
        $webconfirmtext   .= xarML('You should receive confirmation of your email within a few minutes.');
        xarModSetVar('sitecontact','webconfirmtext',$webconfirmtext);
    }
    $data['webconfirmtext']=$webconfirmtext;
    if ($message == 1 && $antibotinvalid != TRUE) {
        $data['messagetxt']= $data['webconfirmtext'];
         $data['message']=$message;
    } else {
        $data['message']='';
        $data['messagetxt'] = '';
    }
    //initialize an array used for error holding later
    $data['invalid']=array();
    
    $data['scid']=$formdata['scid'];
    $data['sctypename']=$formdata['sctypename'];
    $data['permissioncheck']=$formdata['permissioncheck'];
    $data['savedata']=$formdata['savedata'];
    $data['permission']=false; //set it to false and require user to check
    $data['termslink']=trim($formdata['termslink']);
    if (!empty($data['sctypename'])){
        $template =  $data['sctypename'];
    } else {
        $template =  '';
    }
    $data['scform']=$data['sctypename'];
    //include custom functions for preprocessing data
    $customfunc = 'modules/sitecontact/xarworkflowapi/'.$sctypename.'.php';
    if (file_exists($customfunc)) {
         include_once($customfunc);
    }
    //backward compatibility
    if (xarModIsAvailable('formantibot')) {
        $data['AntiBot_Available'] = TRUE;
    }

    $templatedata = xarTplModule('sitecontact', 'user', 'main', $data, $template);

   return $templatedata;
}
?>
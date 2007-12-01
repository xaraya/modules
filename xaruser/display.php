<?php
/**
 * Main user function
 *
 * @package modules
 * @copyright (C) 2002-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage SiteContact Module
 * @link http://xaraya.com/index.php/release/890.html
 * @author Jo Dalle Nogare <jojodee@xaraya.com>
 */

/**
 * Main user function
 * This function is the default function, and is called whenever the module is
 * initiated without defining arguments.
 *
 * @author Jo Dalle Nogare
 */

sys::import('modules.dynamicdata.class.objects.master');

function sitecontact_user_display($args)
{
    extract($args);

    $defaultformid=(int)xarModVars::get('sitecontact','defaultform');
    
    $form = DataObjectMaster::getObject(array('name' => 'sitecontact_definition'));
    
    if(!xarVarFetch('sctypename',    'pre:trim:passthru:str:0:',  $sctypename,     NULL, XARVAR_NOT_REQUIRED)) {return;}
    if(!xarVarFetch('company',       'str:1:',  $company,        NULL, XARVAR_NOT_REQUIRED, XARVAR_PREP_FOR_DISPLAY)) return;
    if(!xarVarFetch('message',       'isset',   $message,        NULL, XARVAR_DONT_SET)) {return;}
    if(!xarVarFetch('antibotinvalid','int:0:1', $antibotinvalid, NULL, XARVAR_NOT_REQUIRED)) {return;}
    if(!xarVarFetch('requesttext',   'isset',   $requesttext,    NULL, XARVAR_NOT_REQUIRED)) {return;}
    if(!xarVarFetch('usermessage',   'isset',   $usermessage,    NULL, XARVAR_NOT_REQUIRED)) {return;}
    if(!xarVarFetch('scform',        'pre:trim:passthru:str:0:',  $scform,         NULL, XARVAR_NOT_REQUIRED)) {return;}
    if(!xarVarFetch('botreset',      'bool',    $botreset,       false, XARVAR_NOT_REQUIRED)) {return;}
    if(!xarVarFetch('scid',          'int:1:',  $scid,           $defaultformid, XARVAR_NOT_REQUIRED)) {return;}
    if (!xarVarFetch('userreferer',  'str:1:',  $userreferer,    '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('submitted',    'int:0:1', $submitted,      0,  XARVAR_NOT_REQUIRED)) return;

    $formdata = array();
    
    if (isset($scform) && !empty($scform)) $sctypename = $scform; //provide alternate entry name

    //Have we got a form that is available and active?
    if (isset($sctypename) && !empty($sctypename)) {
       $formdata = xarModAPIFunc('sitecontact','user','getcontacttypes',array('sctypename'=> $sctypename));
    }elseif (!empty($scid)) {
       $formdata = xarModAPIFunc('sitecontact','user','getcontacttypes',array('scid' => $scid));
    } else {
        $formdata = xarModAPIFunc('sitecontact','user','getcontacttypes',array('scid' => $defaultformid));
    }

    //Have we got an active form
    if (!is_array($formdata)) { //exists but not active
      //fallback to default form again
      $formdata = xarModAPIFunc('sitecontact','user','getcontacttypes',array('scid' => $defaultformid));
    }
    $formdata = $formdata[0];
    $sctypename = $formdata['sctypename'];

    if ($formdata['scactive'] != 1) { //form but not active
        $msg = xarML('The form requested is not available');
        throw new BadParameterException(null,$msg);
    }

    // Set up defaults returned from any invalid captcha input
    // TODO - set this up for DD fields as well
    $data['company'] = $company;
    $data['usermessage'] = $usermessage;
    $data['antibotinvalid'] =$antibotinvalid;

    /* Security Check */
    if(!xarSecurityCheck('ReadSitecontact')) return;

    if (!empty($invalid)) {
        $data['invalid']=$invalid;
    }
    $data['scform']=$scform;
    $data['scid']=$formdata['scid'];
    $data['sctypename']=$formdata['sctypename'];
    $data['permissioncheck']=$formdata['permissioncheck'];
    $data['savedata']=$formdata['savedata'];
    $data['permission']=false; //set it to false and require user to check
    $data['termslink']=trim($formdata['termslink']);

    if (isset($formdata['soptions'])) {
        $soptions=unserialize($formdata['soptions']);
        if (is_array($soptions)) {
            foreach ($soptions as $k=>$v) {
                $data[trim($k)]=trim($v);
            }
        }
    }

    if (!isset($data['allowbccs'])) $data['allowbccs']=0;
    if (!isset($data['allowccs'])) $data['allowccs']=0;
    if (!isset($data['adminccs'])) $data['adminccs']=0;
    if (!isset($data['admincclist'])) $data['admincclist']='';
    if (!isset($data['allowanoncopy'])) $data['allowanoncopy']=0;
    if (!isset($data['useantibot'])) $data['useantibot']=false;
    if (!isset($data['savedata'])) $data['savedata']=xarModVars::get('sitecontact','savedata')?xarModVars::get('sitecontact','savedata'):0;
    if (!isset($data['permissioncheck'])) $data['permissioncheck']=xarModVars::get('sitecontact','permissioncheck');
    if (!isset($data['termslink'])) $data['termslink']=xarModVars::get('sitecontact','termslink');
    
    $customtext   = $formdata['customtext'];
    $customtitle  = $formdata['customtitle'];
    $usehtmlemail = $formdata['usehtmlemail'];
    $allowcopy    = $formdata['allowcopy'];
    
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
    $data['customtitle'] = xarVarPrepHTMLDisplay($customtitle);
    $data['customtext']  = xarVarPrepHTMLDisplay($customtext);

    $data['usehtmlemail'] = $usehtmlemail;
    $data['allowcopy']    = $allowcopy;

    $optiontext  = $formdata['optiontext'];
    $optionset   = array();
    $selectitem  = array();
    $optionset   = explode(',',$optiontext);
    $data['optionset'] = $optionset;
    $optionitems = array();
    foreach ($optionset as $optionitem) {
        $item = explode(';',$optionitem);
        if (!isset($item[1])) $item[1] = $item[0];
      $optionitems[] = array('id' => $item[0], 'name' => $item[1]);
    }
    $data['options'] = $optionitems;

    $setmail='';
    if (isset($customtitle)){
        xarTplSetPageTitle(xarVarPrepForDisplay(xarML($customtitle)));
    } else {
         xarTplSetPageTitle(xarVarPrepForDisplay(xarML('Site Contact')));
    }
    if (!isset($requesttext) ) {
        $requesttext='';
    }
    $data['requesttext']=$requesttext;


    // get the dataobject for this form
    if ($sctypename != 'sitecontact_basicform') {
        $object = DataObjectMaster::getObject(array('name' => $sctypename));
        if ($submitted ==1) {
            $object->checkInput();
        }
        $data['object'] = $object;
        $data['properties']= $object->getProperties();
    } 

    $data['useripaddress'] = isset($useripaddress)?$useripaddress:xarServerGetVar('REMOTE_ADDR');

    //set of default fields now in DD, we don't want these twice as they have special handling
    $basicform = DataObjectMaster::getObject(array('name' => 'sitecontact_basicform'));
    
    $data['baseproperties']= array_keys($basicform->getProperties());
    
    $data['authid'] = xarSecGenAuthKey('sitecontact');
    $data['submit'] = xarML('Submit');

    try {
        $templatedata = xarTplModule('sitecontact', 'user', 'display', $data, $data['sctypename']);
    } catch (Exception $e) {
        $templatedata = xarTplModule('sitecontact', 'user', 'display', $data);
    }

    return $templatedata;
}
?>
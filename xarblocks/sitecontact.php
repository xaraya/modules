<?php
/**
 * SiteContact Block
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
 * Initialise the sitecontact block
 */
function sitecontact_sitecontactblock_init()
{
    // Initial values when the block is created.
    $defaultformid = xarModGetVar('sitecontact','defaultform');
    return array(
        'formchoice' => $defaultformid,
        'showdd' => false,
        'nocache' => 0, // cache by default
        'pageshared' => 1, // share across pages (change if you use dynamic pubtypes et al.)
        'usershared' => 1, // share across group members
        'cacheexpire' => null
    );
}

/**
 * get information on block
 */
function sitecontact_sitecontactblock_info()
{
    // Values
    return array(
        'text_type' => 'Site Contact Form',
        'module' => 'sitecontact',
        'text_type_long' => 'Display site contact form',
        'allow_multiple' => true,
        'form_refresh' => false,
        'show_preview' => true
    );
}

/**
 * Display sitecontact block
 */
function sitecontact_sitecontactblock_display($blockinfo)
{

    // Get variables from content block
    if (is_string($blockinfo['content'])) {
        $vars = @unserialize($blockinfo['content']);
    } else {
        $vars = $blockinfo['content'];
    }
    if (!isset($vars['blockconfirm'])) {
        $blockconfirm= 0;
    }
    if (!isset($vars['showdd'])) {
        $vars['showdd'] = false;
    }
    if (!isset($vars['formchoice'])) {
        $vars['formchoice'] = xarModGetVar('sitecontact','defaultform');
    }

    // Get the form data
    $formdata = xarModAPIFunc('sitecontact', 'user', 'getcontacttypes', array('scid' => $vars['formchoice']));
    $formdata=$formdata[0];
    $messagetext = trim($formdata['webconfirmtext']);
    $item['messagetext']=$messagetext;
    $returnedform = xarSessionGetVar('sitecontact.sent');
    $invalid = isset($invalid)? $invalid:'';
    $isvalid= isset($isvalid)? $isvalid:false;
    $item['isvalid'] = $isvalid;
    $item = xarSessionGetVar('sitecontact.blockdata');
    if (($returnedform != 1) || ($item['isvalid'] == FALSE)) {
     /*  Generate a onetime authorisation code for this operation */
    $item['authid'] = xarSecGenAuthKey('sitecontact');
    $item['blockconfirm']=0;
    $item['company'] = isset($item['company'])?$item['company']:'';
    $item['usermessage'] = isset($item['usermessage'])?$item['usermessage']:'';
    $item['antibotinvalid'] = isset($item['antibotinvalid'])?$item['antibotinvalid']:'';
    //formcaptcha
    $item['casmsg']= isset($item['casmsg'])?$item['casmsg']:'';
    /*  Generate a onetime authorisation code for this operation */
    $item['authid'] = xarSecGenAuthKey('sitecontact');
    $item['submit'] = xarML('Submit');

    if (isset($formdata['soptions'])) {
        $soptions=unserialize($formdata['soptions']);
        if (is_array($soptions)) {
            foreach ($soptions as $k=>$v) {
                $item[trim($k)]=trim($v);
            }
        }
    }

    if (!isset($item['allowbccs']))$item['allowbccs']=0;
    if (!isset($item['allowccs']))$item['allowccs']=0;
    if (!isset($item['allowanoncopy']))$item['allowanoncopy']=0;
    if (!isset($item['useantibot']))$item['useantibot']=false;
    if (!isset($item['savedata']))$item['savedata']=xarModGetVar('sitecontact','savedata')?xarModGetVar('sitecontact','savedata'):0;
    if (!isset($item['permissioncheck']))$item['permissioncheck']=xarModGetVar('sitecontact','permissioncheck');
    if (!isset($item['termslink']))$item['termslink']=xarModGetVar('sitecontact','termslink');
    if (!isset($item['ccrecipients']))$item['ccrecipients']='';
    if (!isset($item['bccrecipients']))$item['bccrecipients']='';

    $customtext = $formdata['customtext'];
    $customtitle = $formdata['customtitle'];
    $usehtmlemail= $formdata['usehtmlemail'];
    $allowcopy = $formdata['allowcopy'];

    $item['customtitle']=xarVarPrepHTMLDisplay($customtitle);
    $item['customtext'] = xarVarPrepHTMLDisplay($customtext);

    $item['usehtmlemail'] = $usehtmlemail;
    $item['allowcopy'] = $allowcopy;

    $optiontext = $formdata['optiontext'];
    $optionset = array();
    $selectitem=array();
    $optionset=explode(',',$optiontext);
    $data['optionset']=$optionset;
    $optionitems=array();
    foreach ($optionset as $optionitem) {
      $optionitems[]=explode(';',$optionitem);
    }
   $item['optionitems']=$optionitems;
      if (!isset($item['botreset'])) {
      $item['botreset']=false;
    }
   if ($item['botreset']== false) { //we don't want to set referer to our own form on an anti-bot return, keep the original referer
        $HTTP_REFERER = xarServerGetVar('HTTP_REFERER');
        if (empty($HTTP_REFERER)) {
            $HTTP_REFERER = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
        }
        $item['userreferer']=$HTTP_REFERER;
        if (isset($item['userreferer']) && !empty($item['userreferer'])) {
            $item['userreferer']=xarVarPrepForDisplay($item['userreferer']);
        }
        xarSessionDelVar('sitecontact.blockdata');
    } 
    $setmail='';
    
    $custitle = isset($customtitle)?xarML($customtitle):xarML('Site Contact');
    xarTplSetPageTitle(xarVarPrepForDisplay($custitle));

    $item['requesttext']=isset($item['requesttext'])? $item['requesttext']: '';

    $properties = array();
    $withupload = (int) false;
    if (xarModIsHooked('dynamicdata','sitecontact', $formdata['scid']) && $isvalid !=FALSE) {
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

    $item['withupload']=$withupload;
    //$webconfirmtext = trim(xarModGetVar('sitecontact','webconfirmtext'));
    $webconfirmtext = trim($formdata['webconfirmtext']);
    if (empty($webconfirmtext) || !isset($webconfirmtext)) {

        $webconfirmtext = xarML('Your message has been sent.');
        $webconfirmtext  .='<br />';
        $webconfirmtext   .= xarML('You should receive confirmation of your email within a few minutes.');
        xarModSetVar('sitecontact','webconfirmtext',$webconfirmtext);
    }
    $data['webconfirmtext']=$webconfirmtext;

    if (isset($message) && $message == 1 && $antibotinvalid != TRUE) {
        $item['messagetxt']= $item['webconfirmtext'];
         $item['message']=$message;
    } else {
        $item['message']='';
        $item['messagetxt'] = '';
    }

    $item['scid']=$formdata['scid'];
    $item['sctypename']=$formdata['sctypename'];
    $item['permissioncheck']=$formdata['permissioncheck'];
    $item['savedata']=$formdata['savedata'];
    $item['permission']=false; //set it to false and require user to check
    $item['termslink']=trim($formdata['termslink']);

    $item['scform']=$item['sctypename'];

    //include custom functions for preprocessing data
    $customfunc = 'modules/sitecontact/xarworkflowapi/'.$item['sctypename'].'.php';
    if (file_exists($customfunc)) {
         include_once($customfunc);
    }
    //backward compatibility
    if (xarModIsAvailable('formantibot')) {
        $item['AntiBot_Available'] = TRUE;
    }
    $item['showdd']=$vars['showdd'];
    xarSessionSetVar('sitecontact.sent',0);
  }else {
        //just confirm
        $item['returnedform']= $returnedform;
        $item['messagetext']=  $messagetext;
        xarSessionSetVar('sitecontact.sent',0);
        xarSessionDelVar('sitecontact.blockdata');
  }
  // Populate block info and pass to theme
    $item['blockurl'] = xarServerGetCurrentURL(array(),false);
    $blockinfo['content'] = $item;
    return $blockinfo;

}

/**
 * built-in block help/information system.
 */
function sitecontact_sitecontactblock_help()
{
    return '';
}

?>
<?php
/**
 * SiteContact Block
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage SiteContact Module
 * @link http://xaraya.com/index.php/release/890.html
 * @author Jo Dalle Nogare <jojodee@xaraya.com>
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
    // Security check
    if (!xarSecurityCheck('ReadSitecontactBlock', 0, 'Block', $blockinfo['name'])) {return;}

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

   if ($returnedform != 1) {
     /*  Generate a onetime authorisation code for this operation */
    $item['authid'] = xarSecGenAuthKey('sitecontact');

    if (!empty($invalid)) {
        $item['invalid']=$invalid;
    }
    $item['blockconfirm']=0;
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
      if (!isset($botreset)) {
      $botreset=false;
    }
   if ($botreset== false) { //we don't want to set referer to our own form on an anti-bot return, keep the original referer
        $HTTP_REFERER = xarServerGetVar('HTTP_REFERER');
        if (empty($HTTP_REFERER)) {
            $HTTP_REFERER = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
        }
        $item['userreferer']=$HTTP_REFERER;
        if (isset($item['userreferer']) && !empty($item['userreferer'])) {
            $item['userreferer']=xarVarPrepForDisplay($item['userreferer']);
        }
    } else {
        $item['userreferer']=$userreferer;
    }
    $setmail='';
    if (isset($customtitle)){
        xarTplSetPageTitle(xarVarPrepForDisplay(xarML($customtitle)));
    } else {
         xarTplSetPageTitle(xarVarPrepForDisplay(xarML('Site Contact')));
    }
    if (!isset($requesttext) ) {
        $requesttext='';
    }
    $item['requesttext']=$requesttext;

    $properties = null;
        $withupload = (int) false;
            if (xarModIsAvailable('dynamicdata')) {
                // get the Dynamic Object defined for this module
                $object =  xarModAPIFunc('dynamicdata','user','getobject',
                array('module' =>'sitecontact',
                      'itemtype'=>$formdata['scid']));

                if (isset($object) && !empty($object->objectid)) {
                    $properties =& $object->getProperties();
                }
                if (is_array($properties)) {
                    foreach ($properties as $key => $ddprop) {
                        if (isset($ddprop->upload) && $ddprop->upload == true) {
                            $withupload = (int) true;
                        }
                    }
                }
            }
    unset($properties);
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

    if (xarModIsAvailable('formantibot')) {
        $item['AntiBot_Available'] = TRUE;
    }
    $item['showdd']=$vars['showdd'];
    xarSessionSetVar('sitecontact.sent',0);
    $item['return_url'] = xarServerGetCurrentUrl(array(),false);
  }else {
        //just confirm
        $item['returnedform']= $returnedform;
       $item['messagetext']=  $messagetext;
       xarSessionSetVar('sitecontact.sent',0);
  }
  // Populate block info and pass to theme
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
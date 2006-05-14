<?php
/**
 * Main user function
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
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
function sitecontact_user_main()
{
    $defaultformid=(int)xarModGetVar('sitecontact','defaultform');

    if(!xarVarFetch('message',    'isset',  $message,    NULL, XARVAR_DONT_SET)) {return;}
	if(!xarVarFetch('sctypename', 'str:0:', $sctypename, NULL, XARVAR_NOT_REQUIRED)) {return;}
	if(!xarVarFetch('scform',     'str:0:', $scform,     NULL, XARVAR_NOT_REQUIRED)) {return;}
	if(!xarVarFetch('scid',       'int:1:', $scid,       $defaultformid, XARVAR_NOT_REQUIRED)) {return;}
    if (isset($scform) && !isset($sctypename)) { //provide alternate entry name
      $sctypename=$scform;
    }

    /* Security Check */
    if(!xarSecurityCheck('ViewSiteContact')) return;

    /*  Generate a onetime authorisation code for this operation */
    $data['authid'] = xarSecGenAuthKey('sitecontact');

    if (!empty($invalid)) {
        $data['invalid']=$invalid;
    }
    $formdata=array();
    $formdata2=array();
    $data['submit'] = xarML('Submit');
    //See if we have a form name that exists and is active
    if (isset($sctypename) && trim($sctypename) <> '') {
       $formdata = xarModAPIFunc('sitecontact','user','getcontacttypes',array('sctypename'=>$sctypename));
    } elseif (isset($scid) && $scid>0) { //should fall back to default form if not specified
       $formdata2 = xarModAPIFunc('sitecontact','user','getcontacttypes',array('scid'=>$scid));
    } else {
     //hmm something would be wrong
     $formdata2 = xarModAPIFunc('sitecontact','user','getcontacttypes',array('scid'=>$scid));
    }

    //now what have we got ..
    if (!isset($formdata) || empty($formdata)) { //it doesn't exist anymore or is not active
        $formdata=$formdata2[0];
    } else {
        $formdata=$formdata[0];
    }

    if ($formdata['scactive']<>1) { //formdata exists but perhaps not active?
       $formdata2=xarModAPIFunc('sitecontact','user','getcontacttypes',array('scid'=>$scid));
       $formdata=$formdata2[0];
    }
    if (isset($formdata['soptions'])) {
           $soptions=unserialize($formdata['soptions']);
           if (is_array($soptions)) {
               foreach ($soptions as $k=>$v) {
                   $data[$k]=$v;
              }
           }
    }
    
    if (!isset($data['allowbccs']))$data['allowbccs']=0;
    if (!isset($data['allowccs']))$data['allowccs']=0;
    if (!isset($data['savedata']))$data['savedata']=xarModGetVar('sitecontact','savedata')?xarModGetVar('sitecontact','savedata'):0;
    if (!isset($data['permissioncheck']))$data['permissioncheck']=xarModGetVar('sitecontact','permissioncheck');
    if (!isset($data['termslink']))$data['termslink']=xarModGetVar('sitecontact','termslink');

    $customtext = $formdata['customtext'];
    $customtitle = $formdata['customtitle'];
    $usehtmlemail= $formdata['usehtmlemail'];
    $allowcopy = $formdata['allowcopy'];

    $data['customtitle']=xarVarPrepHTMLDisplay($customtitle);
    $data['customtext'] = xarVarPrepHTMLDisplay($customtext);

    $data['usehtmlemail'] = $usehtmlemail;
    $data['allowcopy'] = $allowcopy;

    $optiontext = $formdata['optiontext'];
    $optionset = array();
    $selectitem=array();
    $optionset=explode(',',$optiontext);
    $data['optionset']=$optionset;
    $optionitems=array();
    foreach ($optionset as $optionitem) {
      $optionitems[]=explode(';',$optionitem);
    }
    $data['optionitems']=$optionitems;

    $HTTP_REMOTE_ADDR = xarServerGetVar('REMOTE_ADDR');
    if (empty($HTTP_REMOTE_ADDR)) {
        $HTTP_REMOTE_ADDR= isset($_SERVER['$REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '';
    }
    $data['useripaddress'] = $HTTP_REMOTE_ADDR;
    $HTTP_REFERER = xarServerGetVar('HTTP_REFERER');
    if (empty($HTTP_REFERER)) {
        $HTTP_REFERER = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
    }
    $data['userreferer']=$HTTP_REFERER;
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
    $data['withupload']=$withupload;
    //$webconfirmtext = trim(xarModGetVar('sitecontact','webconfirmtext'));
    $webconfirmtext = trim($formdata['webconfirmtext']);
    if ((empty($webconfirmtext)) || (!isset($webconfirmtext))) {

        $webconfirmtext = xarML('Your message has been sent.');
        $webconfirmtext  .='<br />';
        $webconfirmtext   .= xarML('You should receive confirmation of your email within a few minutes.');
        xarModSetVar('sitecontact','webconfirmtext',$webconfirmtext);
    }
    $data['webconfirmtext']=$webconfirmtext;
    if ($message == 1) {
        $data['messagetxt']= $data['webconfirmtext'];
         $data['message']=$message;
    } else {
        $data['message']='';
        $data['messagetxt'] = '';
    }
    $data['scid']=$formdata['scid'];
    $data['sctypename']=$formdata['sctypename'];
    $data['permissioncheck']=$formdata['permissioncheck'];
    $data['savedata']=$formdata['savedata'];
    $data['termslink']=$formdata['termslink'];    
    if (!empty($data['sctypename'])){
        $template = 'main-' . $data['sctypename'];
    } else {
        $template =  'main';
    }

	$templatedata = xarTplModule('sitecontact', 'user', $template, $data);

	if (xarCurrentErrorID() == 'TEMPLATE_NOT_EXIST') {
        xarErrorHandled();
        $templatedata = xarTplModule('sitecontact', 'user', 'main', $data);
	}
   return $templatedata;

}
?>
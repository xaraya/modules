<?php
/**
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Sitecontact
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
    if(!xarVarFetch('message', 'isset', $message,  NULL, XARVAR_DONT_SET)) {return;}

    /* Security Check */
    if(!xarSecurityCheck('ViewSiteContact')) return;

    /*  Generate a onetime authorisation code for this operation */
    $data['authid'] = xarSecGenAuthKey();
    if (!empty($invalid)) {
        $data['invalid']=$invalid;
    }
    $data['submit'] = xarML('Submit');
    $customtext = xarModGetVar('sitecontact','customtext');
    $customtitle = xarModGetVar('sitecontact','customtitle');
    $usehtmlemail= xarModGetVar('sitecontact', 'usehtmlemail');
    $allowcopy = xarModGetVar('sitecontact', 'allowcopy');

    $data['customtitle']=xarVarPrepHTMLDisplay($customtitle);
    $data['customtext'] = xarVarPrepHTMLDisplay($customtext);

    $data['usehtmlemail'] = $usehtmlemail;
    $data['allowcopy'] = $allowcopy;
    $optiontext = xarModGetVar('sitecontact','optiontext');
    $optionset = array();
    $selectitem=array();
    $optionset=explode(',',$optiontext);
    $data['optionset']=$optionset;
    $optionitems=array();
    foreach ($optionset as $optionitem) {
      $optionitems[]=explode(';',$optionitem);
    }
    $data['optionitems']=$optionitems;

    $HTTP_REMOTE_ADDR = getenv('REMOTE_ADDR');
    if (empty($HTTP_REMOTE_ADDR)) {
        $HTTP_REMOTE_ADDR= isset($_SERVER['$REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '';
    }
    $data['useripaddress'] = $HTTP_REMOTE_ADDR;
    $HTTP_REFERER = getenv('HTTP_REFERER');
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
                $object =& xarModAPIFunc('dynamicdata','user','getobject', array('module' => 'sitecontact'));
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
    $webconfirmtext = trim(xarModGetVar('sitecontact','webconfirmtext'));
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

   return $data;

}
?>
<?php
/**
 * File: $Id:
 * 
 * SiteContact main user function
 * 
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage sitecontact
 * @author SiteContact module development team 
 */
/**
 * the main user function
 * This function is the default function, and is called whenever the module is
 * initiated without defining arguments.
 */
function sitecontact_user_main()
{
 //   global $HTTP_SERVER_VARS;
    if(!xarVarFetch('message', 'isset', $message,  NULL, XARVAR_DONT_SET)) {return;}

//     $message = xarVarCleanFromInput($message);
    // Security Check
	if(!xarSecurityCheck('ViewSiteContact')) return;

    // Generate a onetime authorisation code for this operation
    $data['authid'] = xarSecGenAuthKey();
    if (!empty($invalid)) {
        $data['invalid']=$invalid;
    }
    $data['submit'] = xarML('Submit');
    $customtext = xarModGetVar('sitecontact','customtext');
    $customtitle = xarModGetVar('sitecontact','customtitle');
    $data['customtitle']=xarVarPrepHTMLDisplay($customtitle);
    $data['customtext'] = xarVarPrepHTMLDisplay($customtext);
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
    $altmail='';
    if (isset($customtitle)){
        xarTplSetPageTitle(xarVarPrepForDisplay(xarML($customtitle)));
    } else {
         xarTplSetPageTitle(xarVarPrepForDisplay(xarML('Site Contact')));
    }
    if (!isset($requesttext) ) {
        $requesttext='';
    }
    $data['requesttext']=$requesttext;

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

    // everything else happens in Template for now
    return $data;

}

?>

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
 * @ Function: contactus
 * @ Param username, useremail, requesttext,company, usermessage,useripaddress,userreferer,altmail
 * @ Author jojodee
 * @ parameters Takes parameters passed by user_sendtofriend to generate info used by email mod
 */
function sitecontact_user_contactus()
{
    // Get parameters
    if (!xarVarFetch('username', 'str:1:', $username, '', XARVAR_NOT_REQUIRED, XARVAR_PREP_FOR_DISPLAY)) return;
    if (!xarVarFetch('useremail', 'str:1:', $useremail, '', XARVAR_NOT_REQUIRED, XARVAR_PREP_FOR_DISPLAY)) return;
    if (!xarVarFetch('requesttext', 'str:1:', $requesttext, '', XARVAR_NOT_REQUIRED, XARVAR_PREP_FOR_DISPLAY)) return;
    if (!xarVarFetch('company', 'str:1:', $company, '', XARVAR_NOT_REQUIRED, XARVAR_PREP_FOR_DISPLAY)) return;
    if (!xarVarFetch('usermessage', 'str:1:', $usermessage, '', XARVAR_NOT_REQUIRED, XARVAR_PREP_FOR_DISPLAY)) return;
    if (!xarVarFetch('useripaddress', 'str:1:', $useripaddress, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('userreferer', 'str:1:', $userreferer, '', XARVAR_NOT_REQUIRED)) return;

    // Confirm authorisation code.
    if (!xarSecConfirmAuthKey()) return;

    // Security Check
//	if(!xarSecurityCheck('ReadSiteContact')) return;
    $notetouser = xarModGetVar('sitecontact','notetouser');
    if (!isset($notetouser)){
        $notetouser = xarModGetVar('sitecontact','defaultnote');
    }
    $optiontext = xarModGetVar('sitecontact','optiontext');
    $optionset = array();
    $selectitem=array();
    $optionset=explode(',',$optiontext);
    $data['optionset']=$optionset;
    $optionitems=array();
    foreach ($optionset as $optionitem) {
      $optionitems[]=explode(';',$optionitem);
    }
    foreach ($optionitems as $optionid) {
      if ($optionid[0]==$requesttext) {
         if (isset($optionid[1])) {
          $altmail=$optionid[1];
         }
      }
    }
   $today = getdate();
    $month = $today['month'];
    $mday = $today['mday'];
    $year = $today['year'];
    $todaydate = $mday.' '.$month.', '.$year;

    $notetouser = preg_replace('/%%username%%/',
                            $username,
                            $notetouser);
    $notetouser = preg_replace('/%%useremail%%/',
                            $useremail,
                            $notetouser);
    $notetouser = preg_replace('/%%requesttext%%/',
                            $requesttext,
                            $notetouser);
    $notetouser = preg_replace('/%%company%%/',
                            $company,
                            $notetouser);
   if (!isset($altmail) ) {
       $adminemail = xarModGetVar('mail','adminmail');
   }else{
       $adminemail=$altmail;
   }

   $adminname= xarModGetVar('mail','adminname');
   $sitename = xarModGetVar('themes','SiteName');
   $siteurl = xarServerGetBaseURL();
    $subject = $requesttext;
    $message = $notetouser;
    $message .="\n\n";
    $message .=xarML('You submitted the following information:');
    $message .="\n\n";
	$message .= xarML('Name:').'           '.$username;
    $message .="\n";
	$message .= xarML('Email:').'           '.$useremail;
    $message .="\n";
	$message .= xarML('Organization:').' '.$company;
    $message .="\n";
	$message .= xarML('Subject:').'        '.$requesttext;
    $message .="\n\n";
	$message .= xarML('Comments:')."\n".$usermessage;
    $message .="\n";
    $message .=('____________________________________________________________');
    $message .="\n\n";
    $message .=xarML('Site Name:')." ".$sitename."\n";
    $message .=xarML('Site URL:')." ".$siteurl."\n";
    $message .="\n\n $todaydate";
    $message .="\n\n";

    $htmlmessage  = xarVarPrepHTMLDisplay($notetouser);
    $htmlmessage .='<br /><br />';
    $htmlmessage .=xarML('You submitted the following information:');
    $htmlmessage .='<br />';
	$htmlmessage .= xarML('Name:').' '.$username;
    $htmlmessage .='<br />';
	$htmlmessage .= xarML('Email:').' '.$useremail;
    $htmlmessage .='<br />';
	$htmlmessage .= xarML('Organization:').' '.$company;
    $htmlmessage .='<br />';
	$htmlmessage .= xarML('Subject: ').$requesttext;
    $htmlmessage .='<br /><br />';
	$htmlmessage .= xarML('Comments:').'<br />'.$usermessage;
    $htmlmessage .='<br /><br />';
    $htmlmessage .=('____________________________________________________________');
    $htmlmessage .='<br /><br />';
    $htmlmessage .=$sitename.' '.xarML('at').' '.$siteurl;
    $htmlmessage .='<br /><br />';


    if (!xarModAPIFunc('mail',
                       'admin',
                       'sendmail',
                       array('info'         => $useremail,
                             'name'         => $username,
                             'subject'      => $subject,
                             'htmlmessage'  => $htmlmessage,
                             'message'      => $message,
                             'from'         => $adminemail,
                             'fromname'     => $adminname))) return;
    //now do admin email
    $adminmessage=xarML('Submitted By:').' '.$username;
    $adminmessage  .="\n";
	$adminmessage  .= ('____________________________________________________________');
    $adminmessage  .="\n";
	$adminmessage  .= xarML('Name:').' '.$username;
    $adminmessage  .="\n";
	$adminmessage  .= xarML('Email:').' '.$useremail;
    $adminmessage  .="\n";
	$adminmessage  .= xarML('Organization:').' '.$company;
    $adminmessage  .="\n";
	$adminmessage  .= xarML('Subject: ').$requesttext;
    $adminmessage  .="\n\n";
	$adminmessage  .= xarML('Comments:')."\n".$usermessage;
    $adminmessage  .="\n";
    $adminmessage  .=('____________________________________________________________');
    $adminmessage  .="\n\n";
    $adminmessage  .=xarML('User information:');
    $adminmessage  .="\n";
    $adminmessage  .=xarML('Sender:').'   '.$useripaddress;
    $adminmessage  .="\n";
    $adminmessage  .=xarML('Referer:').'  '.$userreferer;
    $adminmessage  .="\n";
    $adminmessage  .=('____________________________________________________________');
    $adminmessage  .="\n";
    $adminmessage .=xarML('Site Name:')." ".$sitename."\n";
    $adminmessage .=xarML('Site URL:')." ".$siteurl."\n";
    $adminmessage  .="\n".$todaydate;

    //send email to admin
    if (!xarModAPIFunc('mail',
                       'admin',
                       'sendmail',
                       array('info'         => $adminemail,
                             'name'         => $adminname,
                             'subject'      => $subject,
                             'message'      => $adminmessage,
                             'from'         => $useremail,
                             'fromname'     => $username))) return;

    // lets update status and display updated configuration
    xarResponseRedirect(xarModURL('sitecontact', 'user', 'main', array('message' => '1')));

    // Return
    return true;
}
?>

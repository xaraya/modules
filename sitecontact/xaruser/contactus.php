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
 * @ TODO: convert this all to use templates
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
    if (!xarVarFetch('sendcopy', 'checkbox', $sendcopy, true, XARVAR_NOT_REQUIRED)) return;

    // Confirm authorisation code.
    if (!xarSecConfirmAuthKey()) return;

    // Security Check
//    if(!xarSecurityCheck('ReadSiteContact')) return;
    $notetouser = xarModGetVar('sitecontact','notetouser');
    if (!isset($notetouser)){
        $notetouser = xarModGetVar('sitecontact','defaultnote');
    }
    $usehtmlemail= xarModGetVar('sitecontact', 'usehtmlemail');
    $allowcopy = xarModGetVar('sitecontact', 'allowcopy');
    $optiontext = xarModGetVar('sitecontact','optiontext');
    $optionset = array();
    $selectitem=array();
    $adminemail = xarModGetVar('mail','adminmail');
    $optionset=explode(',',$optiontext);
    $data['optionset']=$optionset;
    $optionitems=array();
    foreach ($optionset as $optionitem) {
      $optionitems[]=explode(';',$optionitem);
    }
    foreach ($optionitems as $optionid) {
        if (trim($optionid[0])==trim($requesttext)) {
            if (isset($optionid[1])) {
                $setmail=$optionid[1];
            }else{
                $setmail=$adminemail;
            }
        }
    }
    if (!isset($setmail) ) {
       $setmail = xarModGetVar('mail','adminmail');
   }
   $data['setmal']=$setmail;
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

   //Get the existing value of htmlmail module
    $oldhtmlvalue=xarModGetVar('mail','html');
    if ($usehtmlemail) {
    //temporarily set the use of html in mail.
        xarModSetVar('mail','html',1);
    } else {
    //make sure at least temporarily it is set to use text in mail
        xarModSetVar('mail','html',0);
    }

    $adminname= xarModGetVar('mail','adminname');
    $sitename = xarModGetVar('themes','SiteName');
    $siteurl = xarServerGetBaseURL();
    $subject = $requesttext;

       //do the html message to be used if html mail is set
        $htmlmessage  = '<br />'.xarVarPrepHTMLDisplay($notetouser);
        $htmlmessage .='<br /><br />';
        $htmlmessage .=xarML('You submitted the following information:');
        $htmlmessage .='<br />';
        $htmlmessage .= xarML('Name:').' '.xarVarPrepHTMLDisplay($username);
        $htmlmessage .='<br />';
        $htmlmessage .= xarML('Email:').' '.$useremail;
        $htmlmessage .='<br />';
        $htmlmessage .= xarML('Organization:').' '.xarVarPrepHTMLDisplay($company);
        $htmlmessage .='<br />';
        $htmlmessage .= xarML('Subject: ').xarVarPrepHTMLDisplay($requesttext);
        $htmlmessage .='<br /><br />';
        $htmlmessage .= xarML('Comments:').'<br />'.xarVarPrepHTMLDisplay($usermessage);
        $htmlmessage .='<br /><br />';
        $htmlmessage .=('____________________________________________________________');
        $htmlmessage .='<br /><br />';
        $htmlmessage .=$sitename.' '.xarML('at').' '.$siteurl;
        $htmlmessage .='<br /><br />'.$todaydate;
        $htmlmessage .='<br /><br />';

        //do the text message
        $message  = "\n".$notetouser;
        $message .="\n\n";
        $message .=xarML('You submitted the following information:');
        $message .="\n\n";
        $message .= xarML('Name:').'           '.$username;
        $message .="\n";
        $message .= xarML('Email:').'           '.$useremail;
        $message .="\n";
        $message .= xarML('Organization:').' '.html_entity_decode($company);
        $message .="\n";
        $message .= xarML('Subject:').'        '.html_entity_decode($requesttext);
        $message .="\n\n";
        $message .= xarML('Comments:')."\n".html_entity_decode($usermessage);
        $message .="\n";
        $message .=('____________________________________________________________');
        $message .="\n\n";
        $message .=xarML('Site Name:')." ".$sitename."\n";
        $message .=xarML('Site URL:')." ".$siteurl."\n";
        $message .="\n\n $todaydate";
        $message .="\n\n";

   if (($allowcopy) and ($sendcopy)) {
    /* let's send a copy of the feedback form to the sender
     * if it is permitted by admin, and the user wants it */
   //send mail to user
    if (!xarModAPIFunc('mail',
                       'admin',
                       'sendmail',
                       array('info'         => $useremail,
                             'name'         => $username,
                             'subject'      => $subject,
                             'message'      => $message,
                             'htmlmessage'  => $htmlmessage,
                             'from'         => $setmail,
                             'fromname'     => $adminname))) return;
    }//allow copy

    //now let's do the html message to admin
    $htmladminmessage   ='<br />'.xarML('Submitted By:').' '.$username;
    $htmladminmessage .='<br /><br />';
    $htmladminmessage  .= ('____________________________________________________________');
    $htmladminmessage  .='<br /><br />';
    $htmladminmessage  .= xarML('Name:').' '.xarVarPrepHTMLDisplay($username);
    $htmladminmessage  .='<br />';
    $htmladminmessage  .= xarML('Email:').' '.$useremail;
    $htmladminmessage  .='<br />';
    $htmladminmessage  .= xarML('Organization:').' '.xarVarPrepHTMLDisplay($company);
    $htmladminmessage  .='<br />';
    $htmladminmessage  .= xarML('Subject: ').xarVarPrepHTMLDisplay($requesttext);
    $htmladminmessage  .='<br /><br />';
    $htmladminmessage  .= xarML('Comments:').'<br />';
    $htmladminmessage  .= xarVarPrepHTMLDisplay($usermessage);
    $htmladminmessage  .='<br /><br />';
    $htmladminmessage  .=('____________________________________________________________');
    $htmladminmessage  .='<br /><br />';
    $htmladminmessage .=xarML('User information:');
    $htmladminmessage .='<br />';
    $htmladminmessage .=xarML('Sender:').'   '.$useripaddress;
    $htmladminmessage  .='<br />';
    $htmladminmessage .=xarML('Referer:').'  '.$userreferer;
    $htmladminmessage .='<br /><br />';
    $htmladminmessage .=('____________________________________________________________');
    $htmladminmessage.='<br /><br />';
    $htmladminmessage .=xarML('Site Name:').' '.$sitename.'<br />';
    $htmladminmessage .=xarML('Site URL:').' '.$siteurl.'<br />';
    $htmladminmessage.='<br /><br />'.$todaydate;
    $htmladminmessage .='<br /><br />';

    //Let's do admin text message
    $adminmessage   ="\n". xarML('Submitted By:').' '.$username;
    $adminmessage  .="\n";
    $adminmessage  .= ('____________________________________________________________');
    $adminmessage  .="\n";
    $adminmessage  .= xarML('Name:').' '.$username;
    $adminmessage  .="\n";
    $adminmessage  .= xarML('Email:').' '.$useremail;
    $adminmessage  .="\n";
    $adminmessage  .= xarML('Organization:').' '.html_entity_decode($company);
    $adminmessage  .="\n";
    $adminmessage  .= xarML('Subject: ').html_entity_decode($requesttext);
    $adminmessage  .="\n\n";
    $adminmessage  .= xarML('Comments:')."\n".html_entity_decode($usermessage);
    $adminmessage  .="\n";
    $adminmessage  .=('____________________________________________________________');
    $adminmessage  .="\n\n";
    $adminmessage  .=xarML('User information:');
    $adminmessage  .="\n";
    $adminmessage  .=xarML('Sender:').'   '.$useripaddress;
    $adminmessage  .="\n";
    $adminmessage  .=xarML('Referer:').'   '.$userreferer;
    $adminmessage  .="\n";
    $adminmessage  .=('____________________________________________________________');
    $adminmessage  .="\n";
    $adminmessage .=xarML('Site Name:').'  '.$sitename."\n";
    $adminmessage .=xarML('Site URL:').' '.$siteurl."\n";
    $adminmessage  .="\n".$todaydate;

    //send email to admin
    if (!xarModAPIFunc('mail',
                       'admin',
                       'sendmail',
                       array('info'         => $setmail,
                             'name'         => $adminname,
                             'subject'      => $subject,
                             'message'      => $adminmessage,
                             'htmlmessage'  => $htmladminmessage,
                             'from'         => $useremail,
                             'fromname'     => $username))) return;

    //set back the original value of html mail in case it has been changed
    xarModSetVar('mail','html',$oldhtmlvalue);

    // lets update status and display updated configuration
    xarResponseRedirect(xarModURL('sitecontact', 'user', 'main', array('message' => '1')));

    // Return
    return true;
}
?>

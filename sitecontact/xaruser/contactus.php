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
    //Prepare the html text message to user
    $htmlsubject = html_entity_decode(xarVarPrepHTMLDisplay($requesttext));
    $htmlcompany = html_entity_decode(xarVarPrepHTMLDisplay($company));
    $htmlusermessage = html_entity_decode(xarVarPrepHTMLDisplay($usermessage));
    $htmlnotetouser = xarVarPrepHTMLDisplay($notetouser);
    $userhtmlmessage= xarTplModule('sitecontact',
                                   'user',
                                   'usermail',
                                    array('notetouser' => $htmlnotetouser,
                                          'username'   => $username,
                                          'useremail'  => $useremail,
                                          'company'    => $htmlcompany,
                                          'requesttext'=> $htmlsubject,
                                          'usermessage'=> $htmlusermessage,
                                          'sitename'   => $sitename,
                                          'siteurl'    => $siteurl,
                                          'todaydate'  => $todaydate),
                                    'html');


        //prepare the text message to user
        $textsubject = html_entity_decode($requesttext);
        $textcompany = html_entity_decode($company);
        $textusermessage = html_entity_decode($usermessage);
        $textnotetouser = $notetouser;
        $usertextmessage= xarTplModule('sitecontact',
                                   'user',
                                   'usermail',
                                    array('notetouser' => $textnotetouser,
                                          'username'   => $username,
                                          'useremail'  => $useremail,
                                          'company'    => $textcompany,
                                          'requesttext'=> $textsubject,
                                          'usermessage'=> $textusermessage,
                                          'sitename'   => $sitename,
                                          'siteurl'    => $siteurl,
                                          'todaydate'  => $todaydate),
                                    'text');


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
                             'message'      => $usertextmessage,
                             'htmlmessage'  => $userhtmlmessage,
                             'from'         => $setmail,
                             'fromname'     => $adminname))) return;
    }//allow copy

    //now let's do the html message to admin

   $adminhtmlmessage= xarTplModule('sitecontact',
                                   'user',
                                   'adminmail',
                                    array('notetouser' => $htmlnotetouser,
                                          'username'   => $username,
                                          'useremail'  => $useremail,
                                          'company'    => $htmlcompany,
                                          'requesttext'=> $htmlsubject,
                                          'usermessage'=> $htmlusermessage,
                                          'sitename'   => $sitename,
                                          'siteurl'    => $siteurl,
                                          'todaydate'  => $todaydate,
                                          'useripaddress' => $useripaddress,
                                          'userreferer' => $userreferer),
                                          'html');

    //Let's do admin text message
  $admintextmessage= xarTplModule('sitecontact',
                                   'user',
                                   'adminmail',
                                    array('notetouser' => $textnotetouser,
                                          'username'   => $username,
                                          'useremail'  => $useremail,
                                          'company'    => $textcompany,
                                          'requesttext'=> $textsubject,
                                          'usermessage'=> $textusermessage,
                                          'sitename'   => $sitename,
                                          'siteurl'    => $siteurl,
                                          'todaydate'  => $todaydate,
                                          'useripaddress' => $useripaddress,
                                          'userreferer' => $userreferer),
                                          'text');
    //send email to admin
    if (!xarModAPIFunc('mail',
                       'admin',
                       'sendmail',
                       array('info'         => $setmail,
                             'name'         => $adminname,
                             'subject'      => $subject,
                             'message'      => $admintextmessage,
                             'htmlmessage'  => $adminhtmlmessage,
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

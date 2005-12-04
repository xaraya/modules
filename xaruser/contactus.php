<?php
/**
 * Contact us main function
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
 * @ Function: contactus
 * @ Author Jo Dalle Nogare <jojodee@xaraya.com>
 * @ Param username, useremail, requesttext,company, usermessage,useripaddress,userreferer,altmail
  */
function sitecontact_user_contactus($args)
{
  extract($args);
    /* Get parameters */
    if (!xarVarFetch('username', 'str:1:', $username, '', XARVAR_NOT_REQUIRED, XARVAR_PREP_FOR_DISPLAY)) return;
    if (!xarVarFetch('useremail', 'str:1:', $useremail, '', XARVAR_NOT_REQUIRED, XARVAR_PREP_FOR_DISPLAY)) return;
    if (!xarVarFetch('requesttext', 'str:1:', $requesttext, '', XARVAR_NOT_REQUIRED, XARVAR_PREP_FOR_DISPLAY)) return;
    if (!xarVarFetch('company', 'str:1:', $company, '', XARVAR_NOT_REQUIRED, XARVAR_PREP_FOR_DISPLAY)) return;
    if (!xarVarFetch('usermessage', 'str:1:', $usermessage, '', XARVAR_NOT_REQUIRED, XARVAR_PREP_FOR_DISPLAY)) return;
    if (!xarVarFetch('useripaddress', 'str:1:', $useripaddress, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('userreferer', 'str:1:', $userreferer, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('sendcopy', 'checkbox', $sendcopy, true, XARVAR_NOT_REQUIRED)) return;

    /* Confirm authorisation code. */
    if (!xarSecConfirmAuthKey()) return;
    $dditems=array();
    $propdata=array();
    if (xarModIsAvailable('dynamicdata')) {
        /* get the Dynamic Object defined for this module (and itemtype, if relevant) */
        $object = xarModAPIFunc('dynamicdata','user','getobject',
                             array('module' => 'sitecontact'));
        if (!isset($object)) return;  /* throw back */

        /* check the input values for this object and do ....what here? */
        $isvalid = $object->checkInput();

        /*we just want a copy of data - don't need to save it in a table (no request yet anyway!) */
        $dditems =& $object->getProperties();

        foreach ($dditems as $itemid => $fields) {
            $items[$itemid] = array();
            foreach ($fields as $name => $value) {
                $items[$itemid][$name] = ($value);
            }

            $propdata=array();
            foreach ($items as $key => $value) {
                $propdata[$value['name']]['label']=$value['label'];
                $propdata[$value['name']]['value']=$value['value'];
            }
        }
     }

    /* Security Check - caused some problems here with anon browsing and cachemanager
     * should be ok now - review
     * if(!xarSecurityCheck('ReadSiteContact')) return;
     */
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
    $mainemail=xarModGetVar('sitecontact','scdefaultemail');

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
                $setmail=$mainemail;
            }
        }
    }
    if (!isset($setmail) ) {
       $setmail = xarModGetVar('sitecontact','scdefaultemail');
   }
   $data['setmail']=$setmail;
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

    $sendname=xarModGetVar('sitecontact','scdefaultname');
    if (!isset($sendname)) {
        $adminname= xarModGetVar('mail','adminname');
        $sendname=$adminname;
    }
    $sitename = xarModGetVar('themes','SiteName');
    $siteurl = xarServerGetBaseURL();
    $subject = $requesttext;

    /* comments in emails is a problem - set it manually for this module
       let's make it contingent on the mail module var - as that is what 
       seems intuitively the correct thing
    */
    $themecomments = xarModGetVar('themes','ShowTemplates');
    $mailcomments = xarModGetVar('mail','ShowTemplates');
    if ($mailcomments == 1) {
        xarModSetVar('themes','ShowTemplates',1);
    } else {
        xarModSetVar('themes','ShowTemplates',0);
    }


    /* Prepare the html text message to user */

    $trans = get_html_translation_table(HTML_ENTITIES);
    $trans = array_flip($trans);
    $htmlsubject = strtr(xarVarPrepHTMLDisplay($requesttext), $trans);
    $htmlcompany = strtr(xarVarPrepHTMLDisplay($company), $trans);
    $htmlusermessage  = strtr(xarVarPrepHTMLDisplay($usermessage), $trans);
    $htmlnotetouser  = strtr(xarVarPrepHTMLDisplay($notetouser), $trans);


    /* jojodee: html_entity_decode only available in php >=4.3
     * $htmlsubject = html_entity_decode(xarVarPrepHTMLDisplay($requesttext));
     * $htmlcompany = html_entity_decode(xarVarPrepHTMLDisplay($company));
     *  $htmlusermessage = html_entity_decode(xarVarPrepHTMLDisplay($usermessage));
     * $htmlnotetouser = xarVarPrepHTMLDisplay($notetouser);
     */

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
                                          'propdata'    => $propdata,
                                          'todaydate'  => $todaydate),
                                    'html');

        /* prepare the text message to user */
        $textsubject = strtr($requesttext,$trans);
        $textcompany = strtr($company,$trans);
        $textusermessage = strtr($usermessage,$trans);
        $textnotetouser = strtr($notetouser,$trans);

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
                                          'propdata'    => $propdata,
                                          'todaydate'  => $todaydate),
                                    'text');


   if (($allowcopy) and ($sendcopy)) {
      /* let's send a copy of the feedback form to the sender
      * if it is permitted by admin, and the user wants it */
      $args = array('info'         => $useremail,
                    'name'         => $username,
                    'subject'      => $subject,
                    'message'      => $usertextmessage,
                    'htmlmessage'  => $userhtmlmessage,
                    'from'         => $setmail,
                    'fromname'     => $sendname,
                    'usetemplates' => false);

        /* send mail to user , if html email let's do that  else just send text*/
        if ($usehtmlemail != 1) {

            if (!xarModAPIFunc('mail',
                       'admin',
                       'sendmail', $args)) return;

        } else {/*it's html email */

            if (!xarModAPIFunc('mail',
                       'admin',
                       'sendhtmlmail', $args)) return;
        }
    }
    /* now let's do the html message to admin */
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
                                          'propdata'    => $propdata,
                                          'userreferer' => $userreferer),
                                          'html');


    /* Let's do admin text message */
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
                                          'propdata'    => $propdata,
                                          'userreferer' => $userreferer),
                                          'text');
    /* send email to admin */
    $args = array('info'         => $setmail,
                  'name'         => $sendname,
                  'subject'      => $subject,
                  'message'      => $admintextmessage,
                  'htmlmessage'  => $adminhtmlmessage,
                  'from'         => $useremail,
                  'fromname'     => $username,
                  'usetemplates' => false);
                  
    if ($usehtmlemail != 1) {

        if (!xarModAPIFunc('mail',
                           'admin',
                           'sendmail', $args))return;
    } else {

        if (!xarModAPIFunc('mail',
                           'admin',
                           'sendhtmlmail', $args))return;
    }
    /* Set the theme comments back */
    xarModSetVar('themes','ShowTemplates',$themecomments);
    /* lets update status and display updated configuration */
    xarResponseRedirect(xarModURL('sitecontact', 'user', 'main', array('message' => '1')));

    /* Return */
    return true;
}
?>
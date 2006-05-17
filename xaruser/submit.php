<?php
/**
 * ITSP initialization functions
 *
 * @package modules
 * @copyright (C) 2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage ITSP Module
 * @link http://xaraya.com/index.php/release/572.html
 * @author ITSP Module Development Team
 */
/**
 * Submit the ITSP
 *
 * When a user submits the ITSP, it is sent to the education office for approval
 * @author MichelV <michelv@xarayahosting.nl>
 * @param int itspid
 * @param string return_url
 * @param bool confirm
 * @param string useraction
 * @since 16 May 2006
 * @return bool true on success of submission
 */
function itsp_user_submit($args)
{
    extract($args);

    /* Get parameters */
    if (!xarVarFetch('itspid', 'id', $itspid, 0, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('return_url',  'isset', $return_url, NULL, XARVAR_DONT_SET)) return;
    if (!xarVarFetch('confirm',  'isset', $confirm, NULL, XARVAR_DONT_SET)) return;
    if (!xarVarFetch('useraction', 'str:1:', $useraction, '', XARVAR_NOT_REQUIRED)) return;

    $data = array();

    if (($itspid < 1) || (empty($useraction))) {
        return $data;
    }
    if(!xarSecurityCheck('ReadITSP', 1, 'itsp', "$itspid:All:All")) {
        return;
    }

/*    //Put all set data in an array for later processing
     $item=array('scid'           => array(xarML('Form ID'),(int)$scid),
                'sctypename'      => array(xarML('Form'),$sctypename),
                'scform'          => array(xarML('Form Name'),$scform),
                'username'        => array(xarML('Name'),$username),
                'useremail'       => array(xarML('Email'),$useremail),
                'requesttext'     => array(xarML('Subject'),$requesttext),
                'company'         => array(xarML('Organization'),$company),
                'usermessage'     => array(xarML('Message'),$usermessage),
                'useripaddress'   => array(xarML('IP'),$useripaddress),
                'userreferer'     => array(xarML('Referrer'),$userreferer),
                'sendcopy'        => array(xarML('Copy?'),$sendcopy),
                'savedata'        => array(xarML('Allow Save?'),$savedata),
                'permissioncheck' => array(xarML('Check permission?'),$permissioncheck),
                'permission'      => array(xarML('Agree to save?'),$permission),
                'termslink'       => array(xarML('Terms provided'),$termslink),
                'bccrecipients'   => array(xarML('BCC'),$bccrecipients),
                'ccrecipients'    => array(xarML('CC'),$ccrecipients)
                );

    /* Confirm authorisation code. */
    if (!xarSecConfirmAuthKey()) return;

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
    }


    /* Security Check - caused some problems here with anon browsing and cachemanager
     * should be ok now - review
     * if(!xarSecurityCheck('ReadSiteContact')) return;
     */

    $notetouser = $formdata['notetouser'];
    if (!isset($notetouser)){
        $notetouser = xarModGetVar('sitecontact','defaultnote');
    }
    $usehtmlemail= $formdata['usehtmlemail'];
    $allowcopy = $formdata['allowcopy'];
    $optiontext = $formdata['optiontext'];
    $optionset = array();
    $selectitem=array();
    $adminemail = xarModGetVar('mail','adminmail');
    $mainemail=$formdata['scdefaultemail'];

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
       $setmail = $formdata['scdefaultemail'];;
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

    $sendname=$formdata['scdefaultname'];;
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
        if (!empty($data['sctypename'])){
             $htmltemplate = 'html-' . $data['sctypename'];
             $texttemplate = 'text-' . $data['sctypename'];
        } else {
             $htmltemplate =  'html';
             $texttemplate =  'text';
        }
       $userhtmlarray= array('notetouser' => $htmlnotetouser,
                              'username'   => $username,
                              'useremail'  => $useremail,
                              'company'    => $htmlcompany,
                              'requesttext'=> $htmlsubject,
                              'usermessage'=> $htmlusermessage,
                              'sitename'   => $sitename,
                              'siteurl'    => $siteurl,
                              'propdata'    => $propdata,
                              'todaydate'  => $todaydate);

        $userhtmlmessage= xarTplModule('sitecontact','user','usermail',$userhtmlarray,$htmltemplate);
        if (xarCurrentErrorID() == 'TEMPLATE_NOT_EXIST') {
            xarErrorHandled();
            $userhtmlmessage= xarTplModule('sitecontact', 'user', 'usermail',$userhtmlarray,'html');
        }
        /* prepare the text message to user */
        $textsubject = strtr($requesttext,$trans);
        $textcompany = strtr($company,$trans);
        $textusermessage = strtr($usermessage,$trans);
        $textnotetouser = strtr($notetouser,$trans);

        $usertextarray =array('notetouser' => $textnotetouser,
                              'username'   => $username,
                              'useremail'  => $useremail,
                              'company'    => $textcompany,
                              'requesttext'=> $textsubject,
                              'usermessage'=> $textusermessage,
                              'sitename'   => $sitename,
                              'siteurl'    => $siteurl,
                              'propdata'    => $propdata,
                              'todaydate'  => $todaydate);

         $usertextmessage= xarTplModule('sitecontact','user','usermail', $usertextarray,$texttemplate);
        if (xarCurrentErrorID() == 'TEMPLATE_NOT_EXIST') {
            xarErrorHandled();
            $usertextmessage= xarTplModule('sitecontact', 'user', 'usermail',$usertextarray,'text');
        }

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
                    'attachName'   => $attachname,
                    'attachPath'   => $attachpath,
                    'usetemplates' => false);

        /* send mail to user , if html email let's do that  else just send text*/
        if ($usehtmlemail != 1) {
            if (!xarModAPIFunc('mail','admin','sendmail', $args)) return;

        } else {/*it's html email */
            if (!xarModAPIFunc('mail','admin','sendhtmlmail', $args)) return;
        }
    }
    /* now let's do the html message to admin */

    $adminhtmlarray=array('notetouser' => $htmlnotetouser,
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
                          'userreferer' => $userreferer);

    $adminhtmlmessage= xarTplModule('sitecontact','user','adminmail',$adminhtmlarray,$htmltemplate);
    if (xarCurrentErrorID() == 'TEMPLATE_NOT_EXIST') {
        xarErrorHandled();
        $adminhtmlmessage= xarTplModule('sitecontact', 'user', 'adminmail',$adminhtmlarray,'html');
    }
    $admintextarray =  array('notetouser' => $textnotetouser,
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
                             'userreferer' => $userreferer);

    /* Let's do admin text message */
    $admintextmessage= xarTplModule('sitecontact','user','adminmail',$admintextarray,$texttemplate);
    if (xarCurrentErrorID() == 'TEMPLATE_NOT_EXIST') {
        xarErrorHandled();
        $admintextmessage= xarTplModule('sitecontact', 'user', 'adminmail',$admintextarray,'text');
    }

    /* send email to admin */
    $args = array('info'         => $setmail,
                  'name'         => $sendname,
                  'ccrecipients' => $ccrecipients,
                  'bccrecipients' => $bccrecipients,
                  'subject'      => $subject,
                  'message'      => $admintextmessage,
                  'htmlmessage'  => $adminhtmlmessage,
                  'from'         => $useremail,
                  'fromname'     => $username,
                  'attachName'   => $attachname,
                  'attachPath'   => $attachpath,
                  'usetemplates' => false);
    if ($usehtmlemail != 1) {
        if (!xarModAPIFunc('mail','admin','sendmail', $args))return;
    } else {
        if (!xarModAPIFunc('mail','admin','sendhtmlmail', $args))return;
    }
    if (isset($attachpath) && !empty($attachpath)){
        if (file_exists($attachpath)) {
            unlink("{$attachpath}");
        }
    }
    /* Set the theme comments back */
    xarModSetVar('themes','ShowTemplates',$themecomments);
    /* lets update status and display updated configuration */
    if (isset($return_url)) {
        xarResponseRedirect(xarModURL($return_url));
    } else {
        xarResponseRedirect(xarModURL('sitecontact', 'user', 'main', array('message' => '1', 'scid'=>$data['scid'])));
    }
    /* Return */
    return true;
}
?>
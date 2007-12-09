<?php
/**
 * Respond function
 *
 * @package modules
 * @copyright (C) 2002-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage SiteContact Module
 * @link http://xaraya.com/index.php/release/890.html
 * @author Jo Dalle Nogare <jojodee@xaraya.com>
 */
/**
 * @ Function: respond
 * @
 * @ API function to handle custom call from other modules
 * @ If customcontact field email is passed it it will override the admin email set in the current form
  */
sys::import('modules.dynamicdata.class.objects.master');

function sitecontact_userapi_respond($args)
{
    extract($args);

    $defaultformid=(int)xarModVars::get('sitecontact','defaultform');

    $formdata = array();
    if (isset($sctypename) && !empty($sctypename)) $sctypename = trim($sctypename);
    if (isset($scform) && !empty($scform)) {//provide alternate entry name
        $scform=trim($scform);
        $sctypename=$scform;
    } else {
        $scform =$sctypename;
    }
    //Have we got a form that is available and active?
    if (isset($sctypename) && !empty($sctypename)) {
       $formdata = xarModAPIFunc('sitecontact','user','getcontacttypes',array('sctypename'=> $sctypename));
    }elseif (!empty($scid)) {
       $formdata = xarModAPIFunc('sitecontact','user','getcontacttypes',array('scid' => $scid));
    } else {
        $formdata = xarModAPIFunc('sitecontact','user','getcontacttypes',array('scid' => $defaultformid));
    }

    //Have we got an active form
    if (!is_array($formdata)) { //exists but not active
      //fallback to default form again
      $formdata = xarModAPIFunc('sitecontact','user','getcontacttypes',array('scid' => $defaultformid));
    }
    $formdata = $formdata[0];
    $sctypename = $formdata['sctypename'];

    if ($formdata['scactive'] != 1) { //form but not active
        $msg = xarML('The form requested is not available');
        throw new BadParameterException(null,$msg);
    }

    //we use the value customcontact field if it exists - this overrides the set admin email.
    if (isset($customcontact) && !empty($customcontact)){
           $newadminemail=$customcontact;
    }else {
           $newadminemail='';
    }

    //now check for the options, and including antibot and - bbccrecipients and ccrecipients switch Bug 5799
     if (isset($formdata['soptions'])) {
           $soptions=unserialize($formdata['soptions']);
           if (is_array($soptions)) {
               foreach ($soptions as $k=>$v) {
                   $soptions[$k]=$v;
              }
           }
           $data['soptions'] = $soptions;
    } else {
           $data['soptions'] = '';
    }

    $useantibot=$soptions['useantibot'];
    $botreset = FALSE; // switch used for referer mainly in main function
    $antibotinvalid = FALSE;
    //just check for valid input and use the isvalid array futher down to return to our display form instead of doing it here
    if (xarModIsAvailable('formantibot') && $useantibot) {
        if (!xarVarFetch('antibotcode',  'str:6:10', $antibotcode, '', XARVAR_NOT_REQUIRED) ||
            !xarModAPIFunc('formantibot', 'user', 'validate', array('userInput' => $antibotcode))) {
                $antibotinvalid = TRUE;
                $botreset = TRUE;
        }
    }

    if (!isset($soptions['allowbccs']) || $soptions['allowbccs']!=1) {
       $bccrecipients='';
       $allowbccs = false;
    } else {
       $allowbccs = true;
    }
    $adminccs = $soptions['adminccs'];
    if (isset($soptions['adminccs']) && $soptions['adminccs'] == TRUE && $soptions['allowccs'] == FALSE) {
      //if admin has a cclist and user ccs is not set
      //use the existing $ccreciepient list which should now hold the admin cclist now
    }elseif (!isset($soptions['allowccs']) || $soptions['allowccs']!= 1) { //if cc list is not set
       $ccrecipients='';
       $allowccs = false;
    } else {
       $allowccs = true;
    }
    //end check for bug 5799
    if (!isset($soptions['allowanoncopy']) || $soptions['allowanoncopy']!=1) {
       $allowanoncopy=false;
    } else {
       $allowanoncopy=true;
    }
    //Feature request for more accurate IP
    //leave the ip capture in the forms - hehehe :)
    $useripaddress=xarModAPIFunc('sitecontact','admin','getcurrentip');

     //Put all set data in an array for later processing
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

    //process options
    //make sure we set them for all instances if we are now checking checkinput
    //and returning to display template so we can make consideration for all forms

    /* process CC Recipient list */
    $ccrecipientarray=array();
    $ccrec=array();
    $cctemp=array();
    if (isset($ccrecipients) && !empty($ccrecipients)) {
      $ccrecipientarray=explode(';',$ccrecipients);
      if (is_array($ccrecipientarray)) {
          foreach ($ccrecipientarray as $recipientkey=>$v) {
             $cctemp[]=explode(',',$v);
          }
          foreach ($cctemp as $recipient=>$values) {
              $ccrec[$values[0]]=isset($values[1])?$values[1]:'';
          }
      }
    }
    $ccrecipients=$ccrec;

    /* process BCC Recipient list */
    $bccrecipientarray=array();
    $bccrec=array();
    $bcctemp=array();
    if (isset($bccrecipients) && !empty($bccrecipients)) {
      $bccrecipientarray=explode(';',$bccrecipients);
      if (is_array($bccrecipientarray)) {
          foreach ($bccrecipientarray as $recipientkey=>$v) {
             $bcctemp[]=explode(',',$v);
          }
          foreach ($bcctemp as $recipient=>$values) {
              $bccrec[$values[0]]=isset($values[1])?$values[1]:'';
          }
      }
    }
    $bccrecipients=$bccrec;
    $optiontext = $formdata['optiontext'];
    $optionset  = array();
    $selectitem = array();
    $optionset  = explode(',',$optiontext);
    $data['optionset']=$optionset;
    $optionitems = array();
    //use the optionitems array later for emails
    foreach ($optionset as $optionitem) {
         $optionitems[] = explode(';',$optionitem);
    }
    //make sure we generalize our return for all forms, not just a special one
    //we still need our request text from the DD so put this back up here out of the isvalid false loop
    $options = array(); //initialize
    foreach ($optionitems as $selectitem=>$value) {
          $options[]=trim($value[0]);
    }

    $data['scid']=$formdata['scid'];
    $data['sctypename']=$formdata['sctypename'];
    $withupload = isset($withupload)? $withupload :(int) false;

    $object = DataObjectMaster::getObject(array('name' => $sctypename));

    $properties = $object->getProperties();

    $isvalid = $object->checkInput();
    //need to check dd
    $permission = $properties['permission']->getValue();
    if (($isvalid == FALSE) || ($antibotinvalid == TRUE)) {

        $data = array('authid'         => xarSecGenAuthKey('sitecontact'),
                      'scid'           => $scid,
                      'sctypename'     => $sctypename,
                      'useantibot'     => $useantibot,
                      'options'        => $options,
                      'customtext'     => $formdata['customtext'],
                      'customtitle'    => $formdata['customtitle'],
                      'usehtmlemail'   => $formdata['usehtmlemail'],
                      'allowcopy'      => $formdata['allowcopy'],
                      'allowccs'       => $allowccs,
                      'allowbccs'      => $allowbccs,
                      'adminccs'       => $adminccs,
                      'bccrecipients'  => $bccrecipients,
                      'ccrecipients'   => $ccrecipients,
                      'requesttext'    => $requesttext,
                      'permissioncheck'=> $permissioncheck,
                      'allowanoncopy'  => $allowanoncopy,
                      'antibotinvalid' => TRUE,
                      'botreset'       => TRUE,
                      'userreferer'    => $userreferer,
                      'savedata'       => $savedata,
                      'useripaddress'  => $useripaddress, //make sure we send something back so no error, but it is captured here :)
                      'isvalid'      => $isvalid
                     );
         return $data;
    } 

    foreach ($properties as $itemid => $fields) {

        if (isset($fields->upload) && $fields->upload == true) {
            $withupload = (int) true;
            $fileuploadfieldname=$itemid;
        }
    }
   
     if ($withupload && isset($fileuploadfieldname) && is_array($items[$fileuploadfieldname]) && !empty($items[$fileuploadfieldname]['value'])) {
       $filebasepath=$items[$fileuploadfieldname]['basePath'];
       $filebasedir=$items[$fileuploadfieldname]['basedir'];
       $fileattachmentname=$items[$fileuploadfieldname]['value'];
       $attachpath=$filebasepath.'/'.$filebasedir.'/'.$fileattachmentname;
       $attachname=$fileattachmentname;
   } else {
       $attachpath='';
       $attachname='';
   }

   $responsetime = time();

    /* Do we want to save the data for this form? */

   if ($savedata) {
       // save the form - let it handle save of the hooked dd
       // First check to see if we needed user permission or not, and if we do the user has agreed
        if (($permissioncheck && $permission) || !$permissioncheck) {
           //ok to save
           $args = array('scid'           => (int)$scid,
                         'scform'          => $scform,
                         'username'        => $username,
                         'useremail'       => $useremail,
                         'requesttext'     => $requesttext,
                         'company'         => $company,
                         'usermessage'     => $usermessage,
                         'useripaddress'   => $useripaddress,
                         'userreferer'     => $userreferer,
                         'sendcopy'        => $sendcopy,
                         'savedata'        => $savedata,
                         'permissioncheck' => $permissioncheck,
                         'permission'      => $permission,
                         'bccrecipients'   => serialize($bccrecipients),
                         'ccrecipients'    => serialize($ccrecipients),
                         'responsetime'    => $responsetime
                    );

         } elseif ($permissioncheck && !$permission) {
           //what to do - better save a 'blank' spot as missing data?
           //let's do that for now
          $args = array('scid'           => (int)$scid,
                        'scform'          => $scform,
                        'username'        => xarML('Missing Value'),
                        'useremail'       => '',
                        'requesttext'     => '',
                        'company'         => '',
                        'usermessage'     => '',
                        'useripaddress'   => '',
                        'userreferer'     => '',
                        'sendcopy'        => 0,
                        'savedata'        => $savedata,
                        'permissioncheck' => $permissioncheck,
                        'permission'      => $permission,
                        'bccrecipients'   => '',
                        'ccrecipients'    => '',
                        'responsetime'    => $responsetime
                    );
        }

        $newscrid = xarModAPIFunc('sitecontact','admin','create',$args);

        if (!$newscrid) {
            //no, don't do anything ... if there is a prob we don't want to disrupt the user feedback
            //on their emailing
            //TODO: workout something for this and any other errors related to create reponse portion of process
        }
    }

    $notetouser = $formdata['notetouser'];
    if (!isset($notetouser)){
        $notetouser = xarModVars::get('sitecontact','defaultnote');
    }
    $usehtmlemail= $formdata['usehtmlemail'];
    $allowcopy = $formdata['allowcopy'];
    $optiontext = $formdata['optiontext'];
    $optionset = array();
    $selectitem=array();
    $adminemail = xarModVars::get('mail','adminmail');
    $mainemail=$formdata['scdefaultemail'];

    //now we need to get the value from DD
    $requestoption = $properties['requesttext']->getValue();
    foreach ($optionitems as $optionid) {
        if (trim($optionid[0])==trim($requestoption)) {
            if (isset($optionid[1])) {
                $setmail=$optionid[1];
            }else{
                $setmail=$mainemail;
            }
        }
    }
    //and the text equivalent of the dd value
    $requesttext = $options[$requestoption];
    if (!isset($setmail) ) {
       $setmail = $formdata['scdefaultemail'];
   }
    $data['setmail']=$setmail;
    //now override with specific admin email from location data
    if (!empty($newadminemail)) {
        $setmail=$newadminemail;
        $data['setmail']=$setmail;
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

    $sendname=$formdata['scdefaultname'];;
    if (!isset($sendname)) {
        $adminname= xarModVars::get('mail','adminname');
        $sendname=$adminname;
    }
    $sitename = xarModVars::get('themes','SiteName');
    $siteurl = xarServer::getBaseURL();
    $subject = $requesttext;

    /* comments in emails is a problem - set it manually for this module
       let's make it contingent on the mail module var - as that is what
       seems intuitively the correct thing
       jojo - need to get rid of this with some generalized solution for xar
    */
    $themecomments = xarModVars::get('themes','ShowTemplates');
    $mailcomments = xarModVars::get('mail','ShowTemplates');
    if ($mailcomments == 1) {
        xarModVars::set('themes','ShowTemplates',1);
    } else {
        xarModVars::set('themes','ShowTemplates',0);
    }


    //set of default fields now in DD, we don't want these twice as they have special handling
    $basicform = DataObjectMaster::getObject(array('name' => 'sitecontact_basicform'));
    $baseproperties = array_keys($basicform->getProperties());

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
         $htmltemplate = 'html_' . $data['sctypename'];
         $texttemplate = 'text_' . $data['sctypename'];
    }

   $userhtmlarray= array('notetouser'      => $htmlnotetouser,
                          'username'       => $username,
                          'useremail'      => $useremail,
                          'company'        => $htmlcompany,
                          'requesttext'    => $htmlsubject,
                          'usermessage'    => $htmlusermessage,
                          'sitename'       => $sitename,
                          'siteurl'        => $siteurl,
                          'properties'     => $properties,
                          'baseproperties' => $baseproperties,
                          'todaydate'      => $todaydate);
    try {
        $userhtmlmessage= xarTplModule('sitecontact','user','usermail',$userhtmlarray, $htmltemplate);
    } catch (Exception $e) {
        $userhtmlmessage= xarTplModule('sitecontact', 'user', 'usermail',$userhtmlarray,'html');
    }


    /* prepare the text message to user */
    $textsubject = strtr($requesttext,$trans);
    $textcompany = strtr($company,$trans);
    $textusermessage = strtr($usermessage,$trans);
    $textnotetouser = strtr($notetouser,$trans);

    $usertextarray =array('notetouser'      => $textnotetouser,
                          'username'        => $username,
                          'useremail'       => $useremail,
                          'company'         => $textcompany,
                          'requesttext'     => $textsubject,
                          'usermessage'     => $textusermessage,
                          'sitename'        => $sitename,
                          'siteurl'         => $siteurl,
                          'properties'      => $properties,
                          'baseproperties'  => $baseproperties,
                          'todaydate'       => $todaydate);

    try {
     $usertextmessage= xarTplModule('sitecontact','user','usermail', $usertextarray,$texttemplate);
    } catch (Exception $e) {
        $usertextmessage= xarTplModule('sitecontact', 'user', 'usermail',$usertextarray,'text');
    }
    if (($allowcopy ) and ($sendcopy)) { //the user wants to copy to self and it is allowed by admin
        /* check the logged in user's email address  and if anon is allowed*/
        $docopy = false;
        if (xarUserIsLoggedIn()) {
            $userofficialemail = trim(strtolower(xarUserGetVar('email')));
            $comparemail = trim(strtolower($useremail));
            if ($userofficialemail == $comparemail) {
                $docopy = true;
            }

        } elseif ($allowanoncopy) {
            $docopy = true;
        } else {
            $docopy = false;
        }
        if ($docopy) { //either they are anon and allowed, or logged in and their email is correct
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
    }

    /* now let's do the html message to admin */

    $adminhtmlarray=array('notetouser'      => $htmlnotetouser,
                          'username'        => $username,
                          'useremail'       => $useremail,
                          'company'         => $htmlcompany,
                          'requesttext'     => $htmlsubject,
                          'usermessage'     => $htmlusermessage,
                          'sitename'        => $sitename,
                          'siteurl'         => $siteurl,
                          'todaydate'       => $todaydate,
                          'useripaddress'   => $useripaddress,
                          'properties'      => $properties,
                          'baseproperties'  => $baseproperties,
                          'userreferer'     => $userreferer);
    //In 2x the itemtype specific template must be present for html and text mail. 
    try {
        $adminhtmlmessage= xarTplModule('sitecontact','user','adminmail',$adminhtmlarray,$htmltemplate);
    } catch (Exception $e) {
        $adminhtmlmessage= xarTplModule('sitecontact', 'user', 'adminmail',$adminhtmlarray,'html');
    }
    $admintextarray =  array('notetouser'      => $textnotetouser,
                             'username'        => $username,
                             'useremail'       => $useremail,
                             'company'         => $textcompany,
                             'requesttext'     => $textsubject,
                             'usermessage'     => $textusermessage,
                             'sitename'        => $sitename,
                             'siteurl'         => $siteurl,
                             'todaydate'       => $todaydate,
                             'useripaddress'   => $useripaddress,
                             'baseproperties'  => $baseproperties,
                             'properties'      => $properties,
                             'userreferer'     => $userreferer);

    /* Let's do admin text message */
    try {
        $admintextmessage= xarTplModule('sitecontact','user','adminmail',$admintextarray,$texttemplate);
    } catch (Exception $e) {
        $admintextmessage= xarTplModule('sitecontact', 'user', 'adminmail',$admintextarray,'text');
    }

    /* send email to admin */
    $args = array('info'          => $setmail,
                  'name'          => $sendname,
                  'ccrecipients'  => $ccrecipients,
                  'bccrecipients' => $bccrecipients,
                  'subject'       => $subject,
                  'message'       => $admintextmessage,
                  'htmlmessage'   => $adminhtmlmessage,
                  'from'          => $useremail,
                  'fromname'      => $username,
                  'attachName'    => $attachname,
                  'attachPath'    => $attachpath,
                  'usetemplates'  => false);
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
    xarModVars::set('themes','ShowTemplates',$themecomments);
    /* lets update status and display updated configuration */
    xarSession::setVar('sitecontact.sent',1);
    $args['isvalid'] = true;
    //let's return our data seeing as we have this intermediate  gui 'respond' function now
    return $args;
}
?>
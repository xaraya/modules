<?php
/**
 * Contact us main function
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
 * @ Function: contactus
 * @ Author Jo Dalle Nogare <jojodee@xaraya.com>
 * @ Param username, useremail, requesttext,company, usermessage,useripaddress,userreferer,altmail
  */
function sitecontact_user_contactus($args)
{
  extract($args);

      $defaultformid=(int)xarModVars::get('sitecontact','defaultform');
    /* Get parameters */
    if (!xarVarFetch('username', 'str:1:', $username, '', XARVAR_NOT_REQUIRED, XARVAR_PREP_FOR_DISPLAY)) return;
    if (!xarVarFetch('useremail', 'str:1:', $useremail, '', XARVAR_NOT_REQUIRED, XARVAR_PREP_FOR_DISPLAY)) return;
    if (!xarVarFetch('requesttext', 'str:1:', $requesttext, '', XARVAR_NOT_REQUIRED, XARVAR_PREP_FOR_DISPLAY)) return;
    if (!xarVarFetch('company', 'str:1:', $company, '', XARVAR_NOT_REQUIRED, XARVAR_PREP_FOR_DISPLAY)) return;
    if (!xarVarFetch('usermessage', 'str:1:', $usermessage, '', XARVAR_NOT_REQUIRED, XARVAR_PREP_FOR_DISPLAY)) return;
    if (!xarVarFetch('useripaddress', 'str:1:', $dummy, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('userreferer', 'str:1:', $userreferer, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('sendcopy', 'checkbox', $sendcopy, true, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('sctypename', 'str:0:', $sctypename, NULL, XARVAR_NOT_REQUIRED)) {return;}
    if (!xarVarFetch('scform',     'str:0:', $scform, NULL, XARVAR_NOT_REQUIRED)) {return;}
    if (!xarVarFetch('scid',       'int:1:', $scid,       $defaultformid, XARVAR_NOT_REQUIRED)) {return;}
    if (!xarVarFetch('bccrecipients', 'str:1', $bccrecipients, '')) return;
    if (!xarVarFetch('ccrecipients', 'str:1', $ccrecipients, '')) return;
    if (!xarVarFetch('return_url',  'isset', $return_url, NULL, XARVAR_DONT_SET)) {return;}
    if (!xarVarFetch('savedata',     'checkbox', $savedata, 0, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('permissioncheck', 'checkbox', $permissioncheck, false, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('permission',   'checkbox', $permission, false, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('termslink',    'str:1:',   $termslink, '', XARVAR_NOT_REQUIRED)) return;

    /* Confirm authorisation code. */
    if (!xarSecConfirmAuthKey()) return;

    $formdata=array();
    if (isset($sctypename) && trim($sctypename) !=''){
        $sctypename=trim($sctypename);
    }
    if (isset($scform) && (trim($scform) !='')) { //provide alternate entry name
      $sctypename=trim($scform);
    }
   //Have we got a form that is available and active?
    if (isset($sctypename) && trim($sctypename) !='') {
       $formdata = xarModAPIFunc('sitecontact','user','getcontacttypes',array('sctypename'=> $sctypename));
    }elseif (isset($scid) && is_int($scid)) {
       $formdata = xarModAPIFunc('sitecontact','user','getcontacttypes',array('scid' => $scid));
    } else {
        $formdata = xarModAPIFunc('sitecontact','user','getcontacttypes',array('scid' => $defaultformid));
    }

    //Have we got an active form
    if (!is_array($formdata)) { //exists but not active
      //fallback to default form again
      $formdata = xarModAPIFunc('sitecontact','user','getcontacttypes',array('scid' => $defaultformid));
    }
    $formdata=$formdata[0];

    if ($formdata['scactive'] !=1) { //form but not active
       $msg = xarML('The form requested is not available');
        xarErrorSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
        return;
    }

    $data['submit'] = xarML('Submit');

    //now check for the options, and including antibot and - bbccrecipient and ccrecipient switch Bug 5799
     if (isset($formdata['soptions'])) {
           $soptions=unserialize($formdata['soptions']);
           if (is_array($soptions)) {
               foreach ($soptions as $k=>$v) {
                   $soptions[$k]=$v;
              }
           }
    }
    $useantibot=$soptions['useantibot'];

    if (xarModIsAvailable('formantibot') && $useantibot) {
        if (!xarVarFetch('antibotcode',  'str:6:10', $antibotcode, '', XARVAR_NOT_REQUIRED) ||
            !xarModAPIFunc('formantibot', 'user', 'validate', array('userInput' => $antibotcode))) {
                $args['company'] = $company;
                $args['scid']   = $scid;
                $args['scform'] = $scform;
                $args['usermessage'] = $usermessage;
                $args['sctypename'] = $sctypename;
                $args['requesttext'] = $requesttext;
                $args['antibotinvalid'] = TRUE;
                $args['botreset']=true;
                $args['userreferer']= $userreferer; //don't loose our original referer
                return xarModFunc('sitecontact', 'user', 'main', $args);
        }
    } else {
       $args['botreset']=false; // switch used for referer mainly in main function
    }

    if (!isset($soptions['allowbccs']) || $soptions['allowbccs']!=1) {
       $bccrecipients='';
    }
    if (!isset($soptions['allowccs']) || $soptions['allowccs']!=1) {
       $ccrecipients='';
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



    $data['scid']=$formdata['scid'];
    $data['sctypename']=$formdata['sctypename'];
    $withupload = isset($withupload)? $withupload :(int) false;
    $dditems=array();
    $propdata=array();
    //Begin 2x
    $info = xarModAPIFunc('dynamicdata','user','getobjectinfo',array('name'=> $data['sctypename']));
    $object = & DataObjectMaster::getObject(array('objectid' => $info['objectid']));
    $object->checkInput();
    $properties = $object->properties;
    var_dump($object->properties['departure_date']->value);exit;

    //End 2x
    /* comment out 1x for now
    if (xarModIsAvailable('dynamicdata')) {
        // get the Dynamic Object defined for this module (and itemtype, if relevant)
        $object = xarModAPIFunc('dynamicdata','user','getobject',
                             array('module' => 'sitecontact',
                                   'itemtype' => $data['scid']));
        if (!isset($object)) return;
        $objectid=$object->objectid;


        // check the input values for this object and do ....what here?
        $isvalid = $object->checkInput();
*/
         if (isset($object) && !empty($object->objectid)) {
             $dditems =& $object->getProperties();
         }

        if (is_array($dditems)) {
            foreach ($dditems as $itemid => $fields) {

                if (isset($fields->upload) && $fields->upload == true) {
                    $withupload = (int) true;
                    $fileuploadfieldname=$itemid;
                }

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
  //   }

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
   //Begin 2x

   $responsetime = time();
   //End 2x
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
                         'scform'          => '',
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

    /* Security Check - caused some problems here with anon browsing and cachemanager
     * should be ok now - review
     * if(!xarSecurityCheck('ReadSiteContact')) return;
     */

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
        $adminname= xarModVars::get('mail','adminname');
        $sendname=$adminname;
    }
    $sitename = xarModVars::get('themes','SiteName');
    $siteurl = xarServer::getBaseURL();
    $subject = $requesttext;

    /* comments in emails is a problem - set it manually for this module
       let's make it contingent on the mail module var - as that is what
       seems intuitively the correct thing
    */
    $themecomments = xarModVars::get('themes','ShowTemplates');
    $mailcomments = xarModVars::get('mail','ShowTemplates');
    if ($mailcomments == 1) {
        xarModVars::set('themes','ShowTemplates',1);
    } else {
        xarModVars::set('themes','ShowTemplates',0);
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
             $htmltemplate = 'html_' . $data['sctypename'];
             //$htmltemplate = 'html-' . $data['sctypename']; Not working in 2x
             $texttemplate = 'text_' . $data['sctypename'];
             //$texttemplate = 'text-' . $data['sctypename']; Not working in 2x
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
                              'properties'  => $properties,
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
                              'propdata'   => $propdata,
                              'properties' => $properties,
                              'todaydate'  => $todaydate);

         //In 2x the itemtype specific template must be present. This never user html or text, and
         //   doesn't falls back to user-usermail-text.xt (or user-usermail-html)
         //Using user-usermail-basic.xt instead for example.. Need an alternative for text and html still

         $usertextmessage= xarTplModule('sitecontact','user','usermail', $usertextarray,$texttemplate);
        if (xarCurrentErrorID() == 'TEMPLATE_NOT_EXIST') {
            xarErrorHandled();
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
        } //end do copy
    } //end user copy to self check

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
                           'properties' => $properties,
                          'userreferer' => $userreferer);
    //In 2x the itemtype specific template must be present. This never user html or text, and
    //   doesn't falls back to user-adminmail-text.xt (or user-adminmail-html)
    //Using user-adminmail-basic.xt instead for example.. Need an alternative for text and html still
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
                             'properties' => $properties,
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
    xarModVars::set('themes','ShowTemplates',$themecomments);
    /* lets update status and display updated configuration */
    xarSessionSetVar('sitecontact.sent',1);

    if (isset($return_url)) {
        xarResponseRedirect($return_url);
    } else {
        xarResponseRedirect(xarModURL('sitecontact', 'user', 'main', array('message' => '1', 'scid'=>$data['scid'])));
    }
    /* Return */
    return true;
}
?>
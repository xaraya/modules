<?php
/**
 * Function to handle processing of the form variables and email responses
 *
 * @package Xaraya
 * @copyright (C) 2004-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 *
 * @subpackage Xarigami SiteContact Module
 * @copyright (C) 2006,2007,2008,2009 2skies.com
 * @link http://xarigami.com/project/sitecontact
 * @author Jo Dalle Nogare <icedlava@2skies.com>
 */
/**
 * API function to handle custom call from other modules
 * 
 * @ Author Jo Dalle Nogare <icedlava@2skies.com>
 * @ Param username, useremail, requesttext,company, usermessage,useripaddress,userreferer,altmail
 * @ param customcontact (optional) will be used if passed in and override the default admin email set for the form
 */
  

function sitecontact_userapi_respond($args)
{
    extract($args);

    $defaultformid=(int)xarModGetVar('sitecontact','defaultform');

    $formdata=array();
    if (isset($sctypename) && !empty($sctypename)) $sctypename = trim($sctypename);
    if (isset($scform) && !empty($scform)) {//provide alternate entry name
        $scform=trim($scform);
        $sctypename=$scform;
    } else {
        $scform =$sctypename;
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
    
    $customfunc = 'modules/sitecontact/xarworkflowapi/'.$formdata['sctypename'].'.php';
    if (file_exists($customfunc)) {
            include_once($customfunc);
    }

    //we use the value customcontact field if it exists - this overrides the set admin email.
    if (isset($customcontact) && !empty($customcontact)){
           $newadminemail=$customcontact;
    }else {
           $newadminemail='';
           $customcontact = '';
    }
    
    $data['submit']  = xarML('Submit');

    $customtext      = $formdata['customtext'];
    $customtitle     = $formdata['customtitle'];
    $usehtmlemail    = $formdata['usehtmlemail'];
    $allowcopy       = $formdata['allowcopy'];
    $webconfirmtext  = $formdata['webconfirmtext'];
    $customtext      = $formdata['customtext'];
    $customtitle     = $formdata['customtitle'];
    $permissioncheck = $formdata['permissioncheck'];
    $sctypedesc      = $formdata['sctypedesc'];
    //now check for the options, and including antibot and - bbccrecipient and ccrecipient switch Bug 5799
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
    $botreset = FALSE; // switch used to ensure we retain the original referer on return
    $antibotinvalid = FALSE;
    $badcaptcha = 0;
    $casmsg = '';
  
    if (!xarUserIsLoggedIn()) { //we want to use captch else don't bother if user is logged in
       
        if (xarModIsAvailable('formcaptcha') && xarModGetVar('formcaptcha','usecaptcha') == true && $useantibot) {
            $autocap =xarModGetVar('formcaptcha','autocaptcha');
            
            $cas_antispam='';
            $badcaptcha = false;
            $casmsg ='';
            $cas_antiselect = intval($antiselect);
            $cas_antiword = $antiword;
            //we don't want to reset the antiword
            if ($autocap == FALSE) {
               include_once 'modules/formcaptcha/xaruser/anticonfig.php';
               $cas_textcount = count($cas_text);
               // Copy the first element to a new last element
               $cas_text[] = $cas_text[0];
               $cas_antispam = $cas_text[$cas_antiselect];
               
            } else {
               $md5code = xarSessionGetVar('formcaptcha');
               xarSessionDelVar('formcaptcha');
            }

            //Determine the correct word
            if (($autocap == TRUE) && (md5($antiword) != $md5code)) {
                  $badcaptcha = true;
                  $casmsg = xarModGetVar('formcaptcha','antierror');
            } elseif (($autocap == FALSE) && ($cas_antispam != $cas_antiword)) {
                  $badcaptcha = true;
                  $casmsg = xarModGetVar('formcaptcha','antierror');
            }
        }
    }

    if (!isset($soptions['allowbccs']) || $soptions['allowbccs']!=1) {
        $bccrecipients='';
        $allowbccs = false;
    } else {
       $allowbccs = true;
    }
    
    $adminccs = isset($soptions['adminccs']) ? $soptions['adminccs']: false;
    $admincclist = isset($soptions['admincclist']) ? $soptions['admincclist']: '';
  //get field requirements
    $fieldconfig= isset($soptions['fieldconfig']) ? $soptions['fieldconfig'] : '';
    $fieldconfigs    = explode(',',$fieldconfig);

    if (($adminccs == TRUE)  && !empty($admincclist)) {
        if (!isset($soptions['allowccs']) || $soptions['allowccs']!= 1) { //no cc list
            $ccrecipients = $soptions['admincclist']; //just put the admincclist in the ccrecipients list and process that
            $adminccs = true;
        } elseif (!empty($ccrecipients)) { //add on the admincclist to the existing cclist
            $ccrecipients =$admincclist.';'.$ccrecipients;
        }
    }
    if (!isset($soptions['allowccs']) || $soptions['allowccs']!= 1) { //if cc list is not set
       $ccrecipients= !empty($ccrecipients) ? $ccrecipients: ''; //in case we had an admin ccs list
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

    //set some dd property values not captured in post vars
    //this is where we actually capture the current user ipaddress
    $useripaddress = xarModAPIFunc('sitecontact','admin','getcurrentip');
    $responsetime = time();
    
      /* process CC Recipient list */
    $ccrecipientarray=array();
    $ccrec=array();
    $cctemp=array();
    if (!empty($ccrecipients)) { //will contain ccrecipient list and admincclist as necessary
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
    //get options
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

    $propdata=array();
    $properties = array();
    $isvalid = isset($isvalid)?$isvalid: TRUE; //default in case no dd is being used
    if (xarModIsHooked('dynamicdata','sitecontact',$data['scid'])) { //cannot assume everyone hooks dd here
        /* get the Dynamic Object defined for this module (and itemtype, if relevant) */
        $object = xarModAPIFunc('dynamicdata','user','getobject',
                 array('moduleid' => xarModGetIdFromName('sitecontact'),
                      'itemtype' => $data['scid']));
                      
        if (!isset($object)) return;  /* throw back */
        $objectid=$object->objectid;      

        if (isset($object) && !empty($object->objectid)) {
            /* check the input values for this object and also our own non-post var items */
            $properties = $object->properties;
            $isvalid = $object->checkInput();
            $dditems = $properties; //backward compatibility
        }
    }
    $invalid =array();
    //options for checkbox list fieldconfig
    $defaultfields = array(
                        'useremail'     =>xarML('Please provide your email.'),
                        'username'      =>xarML('Please provide your name'),
                        'requesttext'   =>xarML('Please select a subject'),
                        'company'       =>xarML('Please enter the name of your organization.'),
                        'usermessage'   =>xarML('Please provide your message text.')
                        );
                        
    //check the default fields
    if (is_array($fieldconfigs) && !empty($fieldconfigs)) {
        foreach ($fieldconfigs as $key=>$config) {
            $value = trim($args[$config]);
            if (empty($value)) {
                $invalid[$config] = $defaultfields[$config];
                $isvalid = FALSE;
            }
        }
    }
    //now check email  - doing it before may overwrite

    if (isset($useremail) && !empty($useremail)){ //some times we may not want a user email - check required in fieldconfig
        $checkemail = xarVarValidate('email', $useremail, true);
        if ($checkemail == FALSE) {
           $isvalid = FALSE;
           $invalid['useremail'] =xarML('You must supply a valid email address');

        }
    }    

   $antibotinvalid =0;//initialize    
   $hookinfo = xarModCallHooks('item', 'submit', $scid, array('itemtype'=>$scid));
   $antibotinvalid = isset($hookinfo['antibotinvalid']) ? $hookinfo['antibotinvalid'] : 0;
    //add everything to an array for easy processing later
    $data = array('authid'         => xarSecGenAuthKey('sitecontact'),
                      'scid'           => $scid,
                      'sctypename'     => $sctypename,
                      'properties'     => $properties,
                      'useremail'      => $useremail,
                      'username'       => $username,
                      'company'        => $company,
                      'usermessage'    => $usermessage,
                      'requesttext'    => $requesttext,
                      'useantibot'     => $useantibot,
                      'options'        => $options,
                      'optionitems'    => $optionitems, //the request text options exploded
                      'customtext'     => $customtext,
                      'customtitle'    => $customtitle,
                      'usehtmlemail'   => $usehtmlemail,
                      'allowcopy'      => $allowcopy,
                      'webconfirmtext' => $webconfirmtext,
                      'customtext'     => $customtext,
                      'customtitle'    => $customtitle,
                      'allowccs'       => $allowccs,
                      'allowbccs'      => $allowbccs,
                      'adminccs'       => $adminccs,
                      'admincclist'    => $admincclist,
                      'bccrecipients'  => $bccrecipients,
                      'ccrecipients'   => $ccrecipients,
                      'requesttext'    => $requesttext,
                      'permission'     => $permission,
                      'permissioncheck'=> $permissioncheck,
                      'allowanoncopy'  => $allowanoncopy,
                      'antibotinvalid' => $antibotinvalid,
                      'botreset'       => $botreset,
                      'badcaptcha'     => $badcaptcha,
                      'casmsg'         => $casmsg,
                      'userreferer'    => $userreferer,
                      'savedata'       => $savedata,
                      'useripaddress'  => $useripaddress,
                      'responsetime'   => $responsetime,
                      'isvalid'        => $isvalid,
                      'invalid'        => $invalid,
                      'return_url'     => $return_url,
                      'blockurl'       => $blockurl,
                      'customcontact'  => $customcontact,
                      'fieldconfig'   => $fieldconfig
                     );

    if (($isvalid == FALSE) || ($antibotinvalid == TRUE) || ($badcaptcha == TRUE) || count($invalid)>0) {
         //now make sure our flags are set appropriately
         $data['isvalid'] = FALSE;
         $data['botreset']= TRUE; //so we do not reset referer
         return $data;
    }

    $data['properties'] = $properties;
    //we are not assuming at this stage everything is in DD
    if (is_array($properties) && !empty($properties)) {
        foreach ($properties as $itemid => $fields) {
            if (isset($fields->upload) && $fields->upload == true) {
                $withupload = (int) true;
                $fileuploadfieldname=$itemid;
            }
            //backward compat  - retain?
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


    /* Do we want to save the data for this form? */
    if ($savedata) {
        // save the form - let it handle save of the hooked dd
        // First check to see if we needed user permission or not, and if we do the user has agreed
        if (($permissioncheck && $permission) || !$permissioncheck) {
            //ok to save
            $args = array('scid'            => (int)$scid,
                          'scform'          => $scform,
                          'sctypedesc'      => $sctypedesc,
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
                          'ccrecipients'    => serialize($ccrecipients)
                    );
        } elseif ($permissioncheck && !$permission) {
            //what to do - better save a 'blank' spot as missing data?
            //let's do that for now
            $args = array('scid'            => (int)$scid,
                          'scform'          => $scform,
                          'sctypedesc'      => $sctypedesc,
                          'username'        => xarML('Missing Value'),
                          'useremail'       => '',
                          'requesttext'     => xarML('Missing Value'),
                          'company'         => '',
                          'usermessage'     => '',
                          'useripaddress'   => '',
                          'userreferer'     => '',
                          'sendcopy'        => 0,
                          'savedata'        => $savedata,
                          'permissioncheck' => $permissioncheck,
                          'permission'      => $permission,
                          'bccrecipients'   => '',
                          'ccrecipients'    => ''
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
        $notetouser = xarModGetVar('sitecontact','defaultnote');
    }
    $usehtmlemail = $formdata['usehtmlemail'];
    $allowcopy    = $formdata['allowcopy'];
    $optiontext   = $formdata['optiontext'];
    $optionset    = array();
    $selectitem   = array();
    $adminemail   = xarModGetVar('mail','adminmail');
    $mainemail    = $formdata['scdefaultemail'];

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
    //now override with specific admin email from location data
    if (!empty($newadminemail)) {
        $setmail=$newadminemail;
        $data['setmail']=$setmail;
    }

    $today = getdate();
    $month = $today['month'];
    $mday  = $today['mday'];
    $year  = $today['year'];
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

    assert('!empty($sctypename); /* sctypename should NOT be empty here, code error */');

    $userhtmlarray= array('notetouser' => $htmlnotetouser,
                          'username'   => $username,
                          'useremail'  => $useremail,
                          'company'    => $htmlcompany,
                          'requesttext'=> $htmlsubject,
                          'usermessage'=> $htmlusermessage,
                          'sctypedesc' => $sctypedesc,
                          'sitename'   => $sitename,
                          'siteurl'    => $siteurl,
                          'propdata'   => $propdata,
                          'properties' => $properties,
                          'todaydate'  => $todaydate);

    $userhtmlmessage= xarTplModule('sitecontact','user','usermail-html-'.$sctypename,$userhtmlarray);
    if (xarCurrentErrorID() == 'TEMPLATE_NOT_EXIST') {
        xarErrorHandled();
        $userhtmlmessage= xarTplModule('sitecontact', 'user', 'usermail-html',$userhtmlarray);
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
                          'sctypedesc' => $sctypedesc,
                          'sitename'   => $sitename,
                          'siteurl'    => $siteurl,
                          'propdata'   => $propdata,
                          'properties' => $properties,
                          'todaydate'  => $todaydate);
                          
    $usertextmessage= xarTplModule('sitecontact','user','usermail-text-'.$sctypename, $usertextarray);

    if (xarCurrentErrorID() == 'TEMPLATE_NOT_EXIST') {
        xarErrorHandled();
        $usertextmessage= xarTplModule('sitecontact', 'user', 'usermail-text',$userhtmlarray);
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
            xarLogMessage("User mail sent for $scform");
        } //end do copy
    } //end user copy to self check

    /* now let's do the html message to admin */

    $adminhtmlarray=array('notetouser'  => $htmlnotetouser,
                          'username'    => $username,
                          'useremail'   => $useremail,
                          'company'     => $htmlcompany,
                          'requesttext' => $htmlsubject,
                          'usermessage' => $htmlusermessage,
                          'sitename'    => $sitename,
                          'sctypedesc' => $sctypedesc,
                          'siteurl'     => $siteurl,
                          'todaydate'   => $todaydate,
                          'useripaddress' => $useripaddress,
                          'propdata'      => $propdata,
                          'properties'    => $properties,
                          'userreferer' => $userreferer);

    $adminhtmlmessage= xarTplModule('sitecontact','user','adminmail-html',$adminhtmlarray,$sctypename);
    if (xarCurrentErrorID() == 'TEMPLATE_NOT_EXIST') {
        xarErrorHandled();
        $adminhtmlmessage= xarTplModule('sitecontact', 'user', 'adminmail-html',$userhtmlarray);
    }
    $admintextarray =  array('notetouser'  => $textnotetouser,
                             'username'    => $username,
                             'useremail'   => $useremail,
                             'company'     => $textcompany,
                             'requesttext' => $textsubject,
                             'usermessage' => $textusermessage,
                             'sitename'    => $sitename,
                             'sctypedesc' => $sctypedesc,
                             'siteurl'     => $siteurl,
                             'todaydate'   => $todaydate,
                             'useripaddress' => $useripaddress,
                             'propdata'      => $propdata,
                             'properties'    => $properties,
                             'userreferer' => $userreferer);

    /* Let's do admin text message */
    $admintextmessage = xarTplModule('sitecontact','user','adminmail-text',$admintextarray, $sctypename);
    if (xarCurrentErrorID() == 'TEMPLATE_NOT_EXIST') {
        xarErrorHandled();
        $admintextmessage= xarTplModule('sitecontact', 'user', 'adminmail-text',$userhtmlarray);
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
    xarLogMessage("Admin mail sent for $scform");
    
    if (isset($attachpath) && !empty($attachpath)){
        if (file_exists($attachpath)) {
            unlink("{$attachpath}");
        }
    }
    /* Set the theme comments back */
    xarModSetVar('themes','ShowTemplates',$themecomments);
    /* lets update status and display updated configuration */
    xarSessionSetVar('sitecontact.sent',1);
    $data['isvalid'] = true;
    //let's return our data seeing as we have this intermediate  gui 'respond' function now
    return $data;
}
?>
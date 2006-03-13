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

      $defaultformid=(int)xarModGetVar('sitecontact','defaultform');
    /* Get parameters */
    if (!xarVarFetch('username', 'str:1:', $username, '', XARVAR_NOT_REQUIRED, XARVAR_PREP_FOR_DISPLAY)) return;
    if (!xarVarFetch('useremail', 'str:1:', $useremail, '', XARVAR_NOT_REQUIRED, XARVAR_PREP_FOR_DISPLAY)) return;
    if (!xarVarFetch('requesttext', 'str:1:', $requesttext, '', XARVAR_NOT_REQUIRED, XARVAR_PREP_FOR_DISPLAY)) return;
    if (!xarVarFetch('company', 'str:1:', $company, '', XARVAR_NOT_REQUIRED, XARVAR_PREP_FOR_DISPLAY)) return;
    if (!xarVarFetch('usermessage', 'str:1:', $usermessage, '', XARVAR_NOT_REQUIRED, XARVAR_PREP_FOR_DISPLAY)) return;
    if (!xarVarFetch('useripaddress', 'str:1:', $useripaddress, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('userreferer', 'str:1:', $userreferer, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('sendcopy', 'checkbox', $sendcopy, true, XARVAR_NOT_REQUIRED)) return;
	if(!xarVarFetch('sctypename', 'str:0:', $sctypename, NULL, XARVAR_NOT_REQUIRED)) {return;}
	if(!xarVarFetch('scform',     'str:0:', $scform, NULL, XARVAR_NOT_REQUIRED)) {return;}
	if(!xarVarFetch('scid',       'int:1:', $scid,       $defaultformid, XARVAR_NOT_REQUIRED)) {return;}
    if (isset($scform) && !isset($sctypename)) { //provide alternate entry name
      $sctypename=$scform;
    }

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

   //now what have we got ..
    if (!isset($formdata) || empty($formdata)) { //it doesn't exist anymore or is not active
        $formdata=$formdata2[0];
    } else {
        $formdata=$formdata[0];
    }

    if ($formdata['scactive']<>1) { //formdata exists but perhaps not active?
       $formdata2=xarModAPIFunc('sitecontact','user','getcontacttypes',array('scid'=>$scid));
       $formdata=$formdata2[0];
    }
    
    $data['scid']=$formdata['scid'];
    $data['sctypename']=$formdata['sctypename'];
    $withupload = isset($withupload)? $withupload :(int) false;
    $dditems=array();
    $propdata=array();
    if (xarModIsAvailable('dynamicdata')) {
        /* get the Dynamic Object defined for this module (and itemtype, if relevant) */
        $object = xarModAPIFunc('dynamicdata','user','getobject',
                             array('module' => 'sitecontact',
                                   'itemtype' => $data['scid']));
        if (!isset($object)) return;  /* throw back */

        /* check the input values for this object and do ....what here? */
        $isvalid = $object->checkInput();

        /*we just want a copy of data - don't need to save it in a table (no request yet anyway!) */
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
    xarResponseRedirect(xarModURL('sitecontact', 'user', 'main', array('message' => '1', 'scid'=>$data['scid'])));

    /* Return */
    return true;
}
?>
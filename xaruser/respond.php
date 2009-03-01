<?php
/**
 * Handle form response
 *
 * @package Xaraya 
 * @copyright (C) 2004-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://xaraya.com
 *
 * @subpackage Xarigami SiteContact Module
 * @copyright (C) 2007,2008,2009 2skies.com
 * @link http://xarigami.com/project/sitecontact
 * @author Jo Dalle Nogare <icedlava@2skies.com>
 */
/**
 * @ Function: respond
 * @ Description: accepts parameters from the user form and pass to the api respond function for processing
 * @ return from api function and display appropriate message to the user, or redisplay form for correct input
 * @ Author Jo Dalle Nogare <icedlava@2skies.com>
 */
function sitecontact_user_respond($args)
{
    extract($args);

    $defaultformid=(int)xarModGetVar('sitecontact','defaultform');

    /* Get parameters */
    if (!xarVarFetch('username',      'str:1:',   $username,    '',    XARVAR_NOT_REQUIRED, XARVAR_PREP_FOR_DISPLAY)) return;
    //use str for email validation so we can sent back a message
    //TODO fix the validations for error msgs
    if (!xarVarFetch('useremail',     'str:1:',    $useremail,  '',    XARVAR_NOT_REQUIRED, XARVAR_PREP_FOR_DISPLAY)) return;
    if (!xarVarFetch('requesttext',   'str:1:',   $requesttext, '',    XARVAR_NOT_REQUIRED, XARVAR_PREP_FOR_DISPLAY)) return;
    if (!xarVarFetch('company',       'str:1:',   $company,     '',    XARVAR_NOT_REQUIRED, XARVAR_PREP_FOR_DISPLAY)) return;
    if (!xarVarFetch('usermessage',   'str:1:',   $usermessage, '',    XARVAR_NOT_REQUIRED, XARVAR_PREP_FOR_DISPLAY)) return;
    if (!xarVarFetch('userreferer',   'str:1:',   $userreferer, '',    XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('sendcopy',      'checkbox', $sendcopy,    true,  XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('sctypename',    'str:0:',   $sctypename,  NULL,  XARVAR_NOT_REQUIRED)) {return;}
    if (!xarVarFetch('scform',        'str:0:',   $scform,      NULL,  XARVAR_NOT_REQUIRED)) {return;}
    if (!xarVarFetch('scid',          'int:1:',   $scid,        $defaultformid, XARVAR_NOT_REQUIRED)) {return;}
    if (!xarVarFetch('bccrecipients', 'str:1',    $bccrecipients, '')) return;
    if (!xarVarFetch('ccrecipients',  'str:1',    $ccrecipients, ''))  return;
    if (!xarVarFetch('return_url',    'isset',    $return_url,  NULL,  XARVAR_DONT_SET)) {return;}
    if (!xarVarFetch('savedata',      'checkbox', $savedata,    0,     XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('permission',    'checkbox', $permission,  false, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('termslink',     'str:1:',   $termslink,   '',    XARVAR_NOT_REQUIRED)) return;
    //formcapcha variables
    if (!xarVarFetch('antiselect',    'int:0:',   $antiselect,  NULL,  XARVAR_NOT_REQUIRED)) {return;}
    if (!xarVarFetch('antiword',      'str:1',    $antiword,    '',    XARVAR_NOT_REQUIRED)) {return;}
    //if (!xarVarFetch('antibotcode',   'str:6:10', $antibotcode, '',    XARVAR_NOT_REQUIRED)) return;
    //custom contact email that can be passed in
    if (!xarVarFetch('customcontact', 'str:0:',   $customcontact, '',  XARVAR_NOT_REQUIRED)) {return;}
    if (!xarVarFetch('return_url',     'str:0:',  $return_url,   '',   XARVAR_NOT_REQUIRED)) {return;}
    if (!xarVarFetch('blockurl',       'str:0:',     $blockurl,     '',   XARVAR_NOT_REQUIRED)) {return;}
    if(!xarVarFetch('botreset',   'bool',   $botreset,     false, XARVAR_NOT_REQUIRED)) {return;}
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
    $customfunc = 'modules/sitecontact/xarworkflowapi/'.$formdata['sctypename'].'.php';
    if (file_exists($customfunc)) {
        include_once($customfunc);
    }
    if ($formdata['scactive'] !=1) { //form but not active
        $msg = xarML('The form requested is not available');
        xarErrorSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
        return;
    }
    $webconfirmtext = trim($formdata['webconfirmtext']);
    if (empty($webconfirmtext) || !isset($webconfirmtext)) {

        $webconfirmtext = xarML('Your message has been sent.');
        $webconfirmtext  .='<br />';
        $webconfirmtext   .= xarML('You should receive confirmation of your email within a few minutes.');
        xarModSetVar('sitecontact','webconfirmtext',$webconfirmtext);
    }
    //check email if it is set and not empty
    $invalid =''; // do not set array here - used as flag
    if (isset($useremail) && !empty($useremail)){
        $checkemail = xarModAPIFunc('roles','user','validatevar',
                              array('var' => $useremail,
                                    'type' => 'email'));
        if ($checkemail == FALSE) {
           $isvalid = FALSE;
           $invalid['useremail'] =xarML('Please enter a valid email');

        }
    }

    //Put all user required data in an array for later processing
     $item=array('scid'            => $formdata['scid'],
                 'sctypename'      => $formdata['sctypename'],
                 'scform'          => $scform,
                 'username'        => $username,
                 'useremail'       => $useremail,
                 'requesttext'     => $requesttext,
                 'company'         => $company,
                 'usermessage'     => $usermessage,
                 'userreferer'     => $userreferer,
                 'sendcopy'        => $sendcopy,
                 'savedata'        => $savedata,
                 'permission'      => $permission,
                 'bccrecipients'   => $bccrecipients,
                 'ccrecipients'    => $ccrecipients,
                 'return_url'      => $return_url,
                 'antiselect'      => $antiselect,
                 'antiword'        => $antiword,
                 'invalid'         => $invalid,
                 'customcontact'   => $customcontact,
                 'return_url'      => $return_url,
                 'blockurl'        => $blockurl,
                 'botreset'         => $botreset
                );
                

    $checkdata = xarModAPIFunc('sitecontact','user','respond', $item);
    $sctypename=$formdata['sctypename'];

    if ($checkdata['isvalid'] != TRUE) {
        // we need to include this again .... we cannot assume we have all vars
        $customfunc = 'modules/sitecontact/xarworkflowapi/'.$formdata['sctypename'].'.php';
        if (file_exists($customfunc)) {
            include_once($customfunc);
        }
         //new hooks
         $item['module'] = 'sitecontact';
         $item['itemid'] = 0;
         $item['itemtype'] = $formdata['scid'];
         $item['antibotinvalid'] = isset($checkdata['antibotinvalid'])?$checkdata['antibotinvalid']:0;
         $checkdata['hooks'] = xarModCallHooks('item','new','',$item);  
               
        if (isset($blockurl) && !empty($blockurl)) {
           xarSessionSetVar('sitecontact.blockdata',$checkdata);
           xarResponseRedirect($blockurl);
           return;
        } else {
            $templatedata = xarTplModule('sitecontact', 'user', 'main', $checkdata, $sctypename);
        }
    } else { //invalid could be null
        $checkdata['result'] = 1;
        if (isset($blockurl) && !empty($blockurl)) {
           xarSessionSetVar('sitecontact.blockdata',$checkdata);
           xarResponseRedirect($blockurl);
           return;
        } else {
            $templatedata = xarTplModule('sitecontact', 'user', 'result', $checkdata, $sctypename);
        }
    }
     return $templatedata;

}
?>
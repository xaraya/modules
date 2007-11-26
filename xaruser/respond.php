<?php
/**
 * Respond function
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
 * @ Function: respond
 * @ Author Jo Dalle Nogare <jojodee@xaraya.com>
 * @ Param username, useremail, requesttext,company, usermessage,useripaddress,userreferer,altmail
  */
function sitecontact_user_respond($args)
{
    extract($args);

    $defaultformid=(int)xarModVars::get('sitecontact','defaultform');

    if (!xarVarFetch('useripaddress', 'str:1:', $dummy, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('userreferer',   'str:1:', $userreferer, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('sctypename',    'str:0:', $sctypename, NULL, XARVAR_NOT_REQUIRED)) {return;}
    if (!xarVarFetch('scform',        'str:0:', $scform, NULL, XARVAR_NOT_REQUIRED)) {return;}
    if (!xarVarFetch('scid',          'int:1:', $scid,       $defaultformid, XARVAR_NOT_REQUIRED)) {return;}
    if (!xarVarFetch('return_url',    'isset',  $return_url, NULL, XARVAR_DONT_SET)) {return;}
    if (!xarVarFetch('savedata',      'checkbox', $savedata, 0, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('username',      'str:1:', $username, '', XARVAR_NOT_REQUIRED, XARVAR_PREP_FOR_DISPLAY)) return;
    if (!xarVarFetch('useremail',     'str:1:', $useremail, '', XARVAR_NOT_REQUIRED, XARVAR_PREP_FOR_DISPLAY)) return;
    if (!xarVarFetch('requesttext',   'str:1:', $requesttext, '', XARVAR_NOT_REQUIRED, XARVAR_PREP_FOR_DISPLAY)) return;
    if (!xarVarFetch('permission',    'checkbox', $permission, false, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('company',       'str:1:', $company, '', XARVAR_NOT_REQUIRED, XARVAR_PREP_FOR_DISPLAY)) return;
    if (!xarVarFetch('usermessage',   'str:1:', $usermessage, '', XARVAR_NOT_REQUIRED, XARVAR_PREP_FOR_DISPLAY)) return;
    if (!xarVarFetch('sendcopy',      'checkbox', $sendcopy, true, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('bccrecipients', 'str:1',  $bccrecipients, '')) return;
    if (!xarVarFetch('ccrecipients',  'str:1',  $ccrecipients, '')) return;
    if (!xarVarFetch('permission',    'checkbox', $permission, false, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('newemail',      'str:1',  $newemail, '')) return;
    if (!xarVarFetch('customcontact', 'str:0:', $customcontact, '',XARVAR_NOT_REQUIRED)) {return;}
    $formdata = array(); 
     if (isset($sctypename) && !empty($sctypename)) $sctypename = trim($sctypename);
    if (!empty($scform)) { //provide alternate entry name
        $scform = trim($scform); 
        $sctypename= $scform;
    }

    //Have we got a form that is available and active?
    if (!empty($sctypename)) {
       $formdata = xarModAPIFunc('sitecontact','user','getcontacttypes',array('sctypename'=> $sctypename));
    }elseif (!empty($scid)) {
       $formdata = xarModAPIFunc('sitecontact','user','getcontacttypes',array('scid' => $scid));
    } else {
        $formdata = xarModAPIFunc('sitecontact','user','getcontacttypes',array('scid' => xarModVars::get('sitecontact','defaultform')));
    }
 

   //Have we got an active form
    if (!is_array($formdata)) { //exists but not active
      //fallback to default form again
      $formdata = xarModAPIFunc('sitecontact','user','getcontacttypes',array('scid' => xarModVars::get('sitecontact','defaultform')));
    }
    $formdata = $formdata[0];
    $data=$formdata;
    $sctypename = $formdata['sctypename'];

    $data['scform']=$scform;
    $data['return_url']=$return_url;   
    $data['userreferer']=$userreferer;
    $data['sctypename']=$sctypename;
    $data['savedata']=$savedata;    
    $data['username']=$username;
    $data['useremail']=$useremail;    
    $data['requesttext']=$requesttext;      
    $data['company']=$company;      
    $data['permission']=$permission;
    $data['ccrecipients']=$ccrecipients; 
    $data['bccrecipients']=$bccrecipients;
    $data['sendcopy']=$sendcopy;  
    $data['usermessage']=$usermessage;   
    $data['customcontact']=$customcontact;       
    if ($data['scactive'] != 1) { //form but not active
        $msg = xarML('The form requested is not available');
        throw new BadParameterException(null,$msg);
    }

    $data['result'] = xarModAPIFunc('sitecontact','user','respond', $data);
    return xarTplModule('sitecontact', 'user', 'result', $data);
}
?>
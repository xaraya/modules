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

    if (!xarVarFetch('useripaddress', 'str:1:', $useripaddress, '', XARVAR_NOT_REQUIRED)) return;
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
    if (!xarVarFetch('bccrecipients', 'str:1',  $bccrecipients, '',XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('ccrecipients',  'str:1',  $ccrecipients, '',XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('permission',    'checkbox', $permission, false, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('newemail',      'str:1',  $newemail, '',XARVAR_NOT_REQUIRED)) return;
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

    $data['scid'] =$scid;
    $data['scform']=$scform;
    $data['return_url']=$return_url;   
    $data['userreferer']=isset($userreferer)?$userreferer:array();
    $data['useripaddress']='';
    $data['return_url']=$return_url;
    $data['sctypename']=$sctypename;
    $data['savedata']=$savedata;    
    $data['username']=$username;
    $data['useremail']=$useremail;    
    $data['requesttext']=$requesttext;      
    $data['company']=$company;      
    $data['permission']=$permission;
    $data['ccrecipients'] = isset($ccrecipients)?$ccrecipients:'';
    $data['bccrecipients']=isset($bccrecipients)?$bccrecipients:'';
    $data['sendcopy']=$sendcopy;  
    $data['usermessage']=$usermessage;   
    $data['customcontact']=$customcontact;       
    if ($data['scactive'] != 1) { //form but not active
        $msg = xarML('The form requested is not available');
        throw new BadParameterException(null,$msg);
    }
    if (isset($formdata['soptions'])) {
           $soptions=unserialize($formdata['soptions']);
           if (is_array($soptions)) {
               foreach ($soptions as $k=>$v) {
                   $soptions[$k]=$v;
              }
           }
           $data['options'] = $soptions;
    } else {
           $data['options'] = '';
    }
  
  
    $checkdata = xarModAPIFunc('sitecontact','user','respond', $data);

    if ($checkdata['isvalid'] == FALSE) {
       $data = $checkdata;
       $basicform = DataObjectMaster::getObject(array('name' => 'sitecontact_basicform'));

       // get the dataobject for this form
       if ($sctypename != 'sitecontact_basicform') {
        $object = DataObjectMaster::getObject(array('name' => $sctypename));
        $data['object'] = $object;
        $data['properties']= $object->getProperties();
        $object->checkInput();
        $data['itemtype'] = $object->itemtype;
        //for backward compat and special cases
        $data['baseproperties']= array_keys($basicform->getProperties());
    } else {
       $data['object'] = $basicform;
       $data['properties']= $basicform->getProperties();
       $basicform->checkInput();
       $data['itemtype'] = $basicform->itemtype;
    }

        // we need to include this again .... we cannot assume we have all vars
        $customfunc = 'modules/sitecontact/xarworkflowapi/'.$sctypename.'.php';
        if (file_exists($customfunc)) {
        include_once($customfunc);
    }
        try {
            $templatedata = xarTplModule('sitecontact', 'user', 'display', $data, $sctypename);
        } catch (Exception $e) {
            $templatedata = xarTplModule('sitecontact', 'user', 'display', $data);
        }
    } else { //invalid could be null
          $data['result'] = 1;
        try {
            $templatedata = xarTplModule('sitecontact', 'user', 'result', $data, $sctypename);
        } catch (Exception $e) {
            $templatedata = xarTplModule('sitecontact', 'user', 'result', $data);
        }
    }
    return $templatedata;
}
?>
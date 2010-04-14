<?php
/**
 * @package modules
 * @copyright (C) 2002-2009 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage registration
 * @link http://xaraya.com/index.php/release/30205.html
 */
/**
 * Modify configuration
 */
function registration_admin_modifyconfig()
{
    // Security Check
    if (!xarSecurityCheck('AdminRegistration')) return;
    if (!xarVarFetch('phase', 'pre:trim:lower:str:1:100', $phase, 'modify', XARVAR_NOT_REQUIRED, XARVAR_PREP_FOR_DISPLAY)) return;
    if (!xarVarFetch('tab', 'pre:trim:lower:str:1:100', $data['tab'], 'general', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('return_url', 'pre:trim:str:1:', $data['return_url'], '', XARVAR_NOT_REQUIRED)) return;

    if ($phase == 'update' && !xarSecConfirmAuthKey())
        return xarTplModule('privileges','user','errors',array('layout' => 'bad_author'));

    if (empty($data['return_url']))
        $data['return_url'] = xarModURL('registration', 'admin', 'modifyconfig', array('tab' => $data['tab']));

    switch ($data['tab']) {
        case 'general':
        default:
            $data['module_settings'] = xarMod::apiFunc('base','admin','getmodulesettings',array('module' => 'registration'));
            $data['module_settings']->setFieldList('enable_short_urls, user_menu_link');
            $data['module_settings']->getItem();
            if ($phase == 'update') {
                if (!xarVarFetch('showterms',   'checkbox', $showterms,   false, XARVAR_NOT_REQUIRED)) return;
                if (!xarVarFetch('showprivacy', 'checkbox', $showprivacy, false, XARVAR_NOT_REQUIRED)) return;
                if (!xarVarFetch('termslink',   'str',      $termslink,   xarModVars::get('registration', 'termslink'), XARVAR_NOT_REQUIRED, XARVAR_PREP_FOR_DISPLAY)) return;
                if (!xarVarFetch('privacylink', 'str',      $privacylink, xarModVars::get('registration', 'privacylink'), XARVAR_NOT_REQUIRED, XARVAR_PREP_FOR_DISPLAY)) return;
                $isvalid = $data['module_settings']->checkInput();
                if ($isvalid) {
                    $data['module_settings']->updateItem();
                    xarModVars::set('registration', 'showterms', $showterms);
                    xarModVars::set('registration', 'showprivacy', $showprivacy);
                    xarModVars::set('registration', 'termslink', $termslink);
                    xarModVars::set('registration', 'privacylink', $privacylink);
                    xarResponse::redirect($data['return_url']);
                    return true;
                }
            }
        break;

        case 'registration':
            if ($phase == 'update') {
                if (!xarVarFetch('defaultgroup',      'int',    $defaultgroup,        xarModVars::get('registration', 'defaultgroup'), XARVAR_NOT_REQUIRED, XARVAR_PREP_FOR_DISPLAY)) return;
                if (!xarVarFetch('defaultuserstate',  'int',    $defaultuserstate,    xarModVars::get('registration', 'defaultuserstate'), XARVAR_NOT_REQUIRED, XARVAR_PREP_FOR_DISPLAY)) return;
                if (!xarVarFetch('allowregistration', 'checkbox', $allowregistration, false, XARVAR_NOT_REQUIRED)) return;
                if (!xarVarFetch('chooseownpassword', 'checkbox', $chooseownpassword, false, XARVAR_NOT_REQUIRED)) return;
                if (!xarVarFetch('minage',            'str:1:3:', $minage,            '13', XARVAR_NOT_REQUIRED, XARVAR_PREP_FOR_DISPLAY)) return;
                if (!xarVarFetch('sendnotice',        'checkbox', $sendnotice,        false, XARVAR_NOT_REQUIRED)) return;
                if (!xarVarFetch('explicitapproval',  'checkbox', $explicitapproval,  false, XARVAR_NOT_REQUIRED)) return;
                if (!xarVarFetch('requirevalidation', 'checkbox', $requirevalidation, false, XARVAR_NOT_REQUIRED)) return;
                if (!xarVarFetch('sendwelcomeemail',  'checkbox', $sendwelcomeemail,  false, XARVAR_NOT_REQUIRED)) return;
                if (!xarVarFetch('notifyemail',       'str:1:150',$notifyemail,       xarModVars::get('mail', 'adminmail'), XARVAR_NOT_REQUIRED)) return;
                if (!xarVarFetch('regobjectname',     'str',      $regobjectname,     xarModVars::get('registration', 'registrationobject'), XARVAR_NOT_REQUIRED, XARVAR_PREP_FOR_DISPLAY)) return;
                if (!xarVarFetch('allowreview',       'checkbox', $allowreview,       false, XARVAR_NOT_REQUIRED)) return;
                if (!xarVarFetch('reviewobjectname',  'str',      $reviewobjectname,  xarModVars::get('registration', 'registrationobject'), XARVAR_NOT_REQUIRED, XARVAR_PREP_FOR_DISPLAY)) return;
                if (!xarVarFetch('usermessage',       'str',      $usermessage,  xarModVars::get('registration', 'usermessage'), XARVAR_NOT_REQUIRED, XARVAR_PREP_FOR_DISPLAY)) return;
                if (!xarVarFetch('adminmessage',      'str',      $adminmessage,  xarModVars::get('registration', 'adminmessage'), XARVAR_NOT_REQUIRED, XARVAR_PREP_FOR_DISPLAY)) return;
                if (!xarVarFetch('notificationmodule','str',      $notificationmodule,  xarModVars::get('registration', 'notificationmodule'), XARVAR_NOT_REQUIRED, XARVAR_PREP_FOR_DISPLAY)) return;

                xarModVars::set('registration', 'chooseownpassword', $chooseownpassword);
                xarModVars::set('registration', 'defaultgroup', $defaultgroup);
                xarModVars::set('registration', 'defaultuserstate', $defaultuserstate);
                xarModVars::set('registration', 'allowregistration', $allowregistration);
                xarModVars::set('registration', 'minage', $minage);
                xarModVars::set('registration', 'notifyemail', $notifyemail);
                xarModVars::set('registration', 'sendnotice', $sendnotice);
                xarModVars::set('registration', 'explicitapproval', $explicitapproval? true:false);
                xarModVars::set('registration', 'requirevalidation', $requirevalidation);
                xarModVars::set('registration', 'sendwelcomeemail', $sendwelcomeemail);
                xarModVars::set('registration', 'registrationobject', $regobjectname);
                xarModVars::set('registration', 'allowreview', $allowreview);
                xarModVars::set('registration', 'reviewobject', $reviewobjectname);
                xarModVars::set('registration', 'usermessage', $usermessage);
                xarModVars::set('registration', 'adminmessage', $adminmessage);
                xarModVars::set('registration', 'notificationmodule', $notificationmodule);
                xarResponse::redirect($data['return_url']);
                return true;
            }
            // create the dropdown of groups for the template display
            // get the array of all groups
            // remove duplicate entries from the list of groups
            $roles  = new xarRoles();
            $groups = array();
            $names  = array();
            foreach($roles->getgroups() as $temp) {
                $nam = $temp['name'];
                if (!in_array($nam, $names)) {
                    array_push($names, $nam);
                    array_push($groups, $temp);
                }
            }
            $data['groups'] = $groups;

            $notifyemail = xarModVars::get('registration','notifyemail');
            if (!isset($notifyemail) || trim ($notifyemail)== '') $notifyemail = xarModVars::get('mail','adminmail');
            $data['notifyemail']=$notifyemail;
        break;

        case 'filtering':
            if ($phase == 'update') {
                if (!xarVarFetch('disallowednames',  'str:1', $disallowednames,  '', XARVAR_NOT_REQUIRED, XARVAR_PREP_FOR_DISPLAY)) return;
                //if (!xarVarFetch('disallowedemails', 'str:1', $disallowedemails, '', XARVAR_NOT_REQUIRED, XARVAR_PREP_FOR_DISPLAY)) return;
                if (!xarVarFetch('disallowedips',    'str:1', $disallowedips,    '', XARVAR_NOT_REQUIRED, XARVAR_PREP_FOR_DISPLAY)) return;
                $disallowednames = serialize($disallowednames);
                xarModVars::set('registration', 'disallowednames', $disallowednames);

                //$disallowedemails = serialize($disallowedemails);
                //xarModVars::set('registration', 'disallowedemails', $disallowedemails);

                $disallowedips = serialize($disallowedips);
                xarModVars::set('registration', 'disallowedips', $disallowedips);
                xarResponse::redirect($data['return_url']);
                return true;
            }
            $checkip = xarModVars::get('registration', 'disallowedips');
            if (empty($checkip)) {
                $ip = serialize('10.0.0.1');
                xarModVars::set('registration', 'disallowedips', $ip);
            }
            $data['ips']    = unserialize(xarModVars::get('registration', 'disallowedips'));
            //$data['emails'] = unserialize(xarModVars::get('registration', 'disallowedemails'));
            $data['names']  = unserialize(xarModVars::get('registration', 'disallowednames'));
        break;

    }

    $data['authid'] = xarSecGenAuthKey();

    return $data;
}
?>
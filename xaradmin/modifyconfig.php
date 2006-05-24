<?php
/**
 * Modify configuration
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Registration module
 * @link http://xaraya.com/index.php/release/30205.html
 */
/**
 * modify configuration
 */
function registration_admin_modifyconfig()
{
    // Security Check
    if (!xarSecurityCheck('AdminRegistration')) return;
    if (!xarVarFetch('phase',    'str:1:100', $phase,      'modify', XARVAR_NOT_REQUIRED, XARVAR_PREP_FOR_DISPLAY)) return;
    if (!xarVarFetch('shorturls','checkbox',  $shorturls,   false, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('tab',      'str:1:100', $data['tab'], 'general', XARVAR_NOT_REQUIRED)) return;
    switch (strtolower($phase)) {
        case 'modify':
        default:
            $data['authid'] = xarSecGenAuthKey();
            switch ($data['tab']) {
                case 'general':
                    $data['shorturlschecked'] = xarModGetVar('registration', 'SupportShortURLs') ? true : false;
					$data['uselockout'] =  xarModGetVar('registration', 'uselockout') ? 'checked' : '';
					$data['lockouttime'] = xarModGetVar('registration', 'lockouttime')? xarModGetVar('registration', 'lockouttime'): 15; //minutes
					$data['lockouttries'] = xarModGetVar('registration', 'lockouttries') ? xarModGetVar('registration', 'lockouttries'): 3;
                    break;
                case 'registration':
					// create the dropdown of groups for the template display
					// get the array of all groups
					// remove duplicate entries from the list of groups
					$roles = new xarRoles();
					$groups = array();
					$names = array();
					foreach($roles->getgroups() as $temp) {
						$nam = $temp['name'];
						if (!in_array($nam, $names)) {
							array_push($names, $nam);
							array_push($groups, $temp);
						}
					}
					$data['groups'] = $groups;
					//Use the same modvar here. It is now putback in Roles again so Roles can use the var too without mod dependencies.
					$data['defaultgroup'] = xarModGetVar('roles', 'defaultgroup');
                    break;
                case 'filtering':
					$checkip = xarModGetVar('registration', 'disallowedips');
					if (empty($checkip)) {
						$ip = serialize('10.0.0.1');
						xarModSetVar('registration', 'disallowedips', $ip);
					}
					$data['ips'] = unserialize(xarModGetVar('registration', 'disallowedips'));
					$data['emails'] = unserialize(xarModGetVar('registration', 'disallowedemails'));
					$data['names'] = unserialize(xarModGetVar('registration', 'disallowednames'));
                    break;
                default:
                    break;
            }

            break;

        case 'update':
            // Confirm authorisation code
            if (!xarSecConfirmAuthKey()) return;
            switch ($data['tab']) {
                case 'general':
                default:
                    if (!xarVarFetch('showterms', 'checkbox', $showterms, false, XARVAR_NOT_REQUIRED)) return;
                    if (!xarVarFetch('showprivacy', 'checkbox', $showprivacy, false, XARVAR_NOT_REQUIRED)) return;
                    if (!xarVarFetch('uselockout', 'checkbox', $uselockout, true, XARVAR_NOT_REQUIRED)) return;
                    if (!xarVarFetch('lockouttime', 'int:1:', $lockouttime, 15, XARVAR_NOT_REQUIRED, XARVAR_PREP_FOR_DISPLAY)) return;
                    if (!xarVarFetch('lockouttries', 'int:1:', $lockouttries, 3, XARVAR_NOT_REQUIRED, XARVAR_PREP_FOR_DISPLAY)) return;
                    xarModSetVar('registration', 'SupportShortURLs', $shorturls);
                    xarModSetVar('registration', 'showterms', $showterms);
                    xarModSetVar('registration', 'showprivacy', $showprivacy);
                    xarModSetVar('registration', 'uselockout', $uselockout);
                    xarModSetVar('registration', 'lockouttime', $lockouttime);
                    xarModSetVar('registration', 'lockouttries', $lockouttries);
                    break;
                case 'registration':
                    if (!xarVarFetch('defaultgroup', 'str:1', $defaultgroup, 'Users', XARVAR_NOT_REQUIRED, XARVAR_PREP_FOR_DISPLAY)) return;
                    if (!xarVarFetch('allowregistration', 'checkbox', $allowregistration, false, XARVAR_NOT_REQUIRED)) return;
                    if (!xarVarFetch('chooseownpassword', 'checkbox', $chooseownpassword, false, XARVAR_NOT_REQUIRED)) return;
                    if (!xarVarFetch('minage', 'str:1:3:', $minage, '13', XARVAR_NOT_REQUIRED, XARVAR_PREP_FOR_DISPLAY)) return;
                    if (!xarVarFetch('sendnotice', 'checkbox', $sendnotice, false, XARVAR_NOT_REQUIRED)) return;
                    if (!xarVarFetch('explicitapproval', 'checkbox', $explicitapproval, false, XARVAR_NOT_REQUIRED)) return;
                    if (!xarVarFetch('requirevalidation', 'checkbox', $requirevalidation, false, XARVAR_NOT_REQUIRED)) return;
                    if (!xarVarFetch('showdynamic', 'checkbox', $showdynamic, false, XARVAR_NOT_REQUIRED)) return;
                    if (!xarVarFetch('sendwelcomeemail', 'checkbox', $sendwelcomeemail, false, XARVAR_NOT_REQUIRED)) return;
                    if (!xarVarFetch('minpasslength', 'int:1', $minpasslength, 5, XARVAR_NOT_REQUIRED)) return;
                    if (!xarVarFetch('uniqueemail', 'checkbox', $uniqueemail, xarModGetVar('registration', 'uniqueemail'), XARVAR_NOT_REQUIRED)) return;
                    xarModSetVar('registration', 'chooseownpassword', $chooseownpassword);
                    xarModSetVar('roles', 'defaultgroup', $defaultgroup);
                    xarModSetVar('registration', 'allowregistration', $allowregistration);
                    xarModSetVar('registration', 'minage', $minage);
                    xarModSetVar('registration', 'sendnotice', $sendnotice);
                    xarModSetVar('registration', 'explicitapproval', $explicitapproval? true:false);
                    xarModSetVar('registration', 'requirevalidation', $requirevalidation);
                    xarModSetVar('registration', 'showdynamic', $showdynamic);
                    xarModSetVar('registration', 'sendwelcomeemail', $sendwelcomeemail);
                    xarModSetVar('registration', 'minpasslength', $minpasslength);
                    xarModSetVar('registration', 'uniqueemail', $uniqueemail);
                    break;
                case 'filtering':
                    if (!xarVarFetch('disallowednames', 'str:1', $disallowednames, '', XARVAR_NOT_REQUIRED, XARVAR_PREP_FOR_DISPLAY)) return;
                    if (!xarVarFetch('disallowedemails', 'str:1', $disallowedemails, '', XARVAR_NOT_REQUIRED, XARVAR_PREP_FOR_DISPLAY)) return;
                    if (!xarVarFetch('disallowedips', 'str:1', $disallowedips, '', XARVAR_NOT_REQUIRED, XARVAR_PREP_FOR_DISPLAY)) return;
                    $disallowednames = serialize($disallowednames);
                    xarModSetVar('registration', 'disallowednames', $disallowednames);

                    $disallowedemails = serialize($disallowedemails);
                    xarModSetVar('registration', 'disallowedemails', $disallowedemails);

                    $disallowedips = serialize($disallowedips);
                    xarModSetVar('registration', 'disallowedips', $disallowedips);
                    break;
            }

            xarResponseRedirect(xarModURL('registration', 'admin', 'modifyconfig',array('tab' => $data['tab'])));
            // Return
            return true;
            break;
    }
    return $data;
}
?>
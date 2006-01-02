<?php
/**
 * Modify configuration
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Authentication module
 */
/**
 * modify configuration
 */
function authentication_admin_modifyconfig()
{
    // Security Check
    if (!xarSecurityCheck('AdminAuthentication')) return;
    if (!xarVarFetch('phase', 'str:1:100', $phase, 'modify', XARVAR_NOT_REQUIRED, XARVAR_PREP_FOR_DISPLAY)) return;
    if (!xarVarFetch('tab', 'str:1:100', $data['tab'], 'general', XARVAR_NOT_REQUIRED)) return;
    switch (strtolower($phase)) {
        case 'modify':
        default:
            $data['authid'] = xarSecGenAuthKey();
            switch ($data['tab']) {
                case 'general':
					$data['uselockout'] =  xarModGetVar('authentication', 'uselockout') ? 'checked' : '';
					$data['lockouttime'] = xarModGetVar('authentication', 'lockouttime')? xarModGetVar('authentication', 'lockouttime'): 15; //minutes
					$data['lockouttries'] = xarModGetVar('authentication', 'lockouttries') ? xarModGetVar('authentication', 'lockouttries'): 3;
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
					$data['defaultgroup'] = xarModGetVar('authentication', 'defaultgroup');
                    break;
                case 'filtering':
					$checkip = xarModGetVar('authentication', 'disallowedips');
					if (empty($checkip)) {
						$ip = serialize('10.0.0.1');
						xarModSetVar('authentication', 'disallowedips', $ip);
					}
					$data['ips'] = unserialize(xarModGetVar('authentication', 'disallowedips'));
					$data['emails'] = unserialize(xarModGetVar('authentication', 'disallowedemails'));
					$data['names'] = unserialize(xarModGetVar('authentication', 'disallowednames'));
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

                    xarModSetVar('authentication', 'showterms', $showterms);
                    xarModSetVar('authentication', 'showprivacy', $showprivacy);
                    xarModSetVar('authentication', 'uselockout', $uselockout);
                    xarModSetVar('authentication', 'lockouttime', $lockouttime);
                    xarModSetVar('authentication', 'lockouttries', $lockouttries);
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
                    if (!xarVarFetch('uniqueemail', 'checkbox', $uniqueemail, xarModGetVar('authentication', 'uniqueemail'), XARVAR_NOT_REQUIRED)) return;
                    xarModSetVar('authentication', 'chooseownpassword', $chooseownpassword);
                    xarModSetVar('authentication', 'defaultgroup', $defaultgroup);
                    xarModSetVar('authentication', 'allowregistration', $allowregistration);
                    xarModSetVar('authentication', 'minage', $minage);
                    xarModSetVar('authentication', 'sendnotice', $sendnotice);
                    xarModSetVar('authentication', 'explicitapproval', $explicitapproval);
                    xarModSetVar('authentication', 'requirevalidation', $requirevalidation);
                    xarModSetVar('authentication', 'showdynamic', $showdynamic);
                    xarModSetVar('authentication', 'sendwelcomeemail', $sendwelcomeemail);
                    xarModSetVar('authentication', 'minpasslength', $minpasslength);
                    xarModSetVar('authentication', 'uniqueemail', $uniqueemail);
                    break;
                case 'filtering':
                    if (!xarVarFetch('disallowednames', 'str:1', $disallowednames, '', XARVAR_NOT_REQUIRED, XARVAR_PREP_FOR_DISPLAY)) return;
                    if (!xarVarFetch('disallowedemails', 'str:1', $disallowedemails, '', XARVAR_NOT_REQUIRED, XARVAR_PREP_FOR_DISPLAY)) return;
                    if (!xarVarFetch('disallowedips', 'str:1', $disallowedips, '', XARVAR_NOT_REQUIRED, XARVAR_PREP_FOR_DISPLAY)) return;
                    $disallowednames = serialize($disallowednames);
                    xarModSetVar('authentication', 'disallowednames', $disallowednames);

                    $disallowedemails = serialize($disallowedemails);
                    xarModSetVar('authentication', 'disallowedemails', $disallowedemails);

                    $disallowedips = serialize($disallowedips);
                    xarModSetVar('authentication', 'disallowedips', $disallowedips);
                    break;
            }

            xarResponseRedirect(xarModURL('authentication', 'admin', 'modifyconfig',array('tab' => $data['tab'])));
            // Return
            return true;
            break;
    }
    return $data;
}
?>

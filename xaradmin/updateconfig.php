<?php
/**
 * AuthURL Administrative Display Functions
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage AuthURL
 * @link http://xaraya.com/index.php/release/42241.html
 * @author Court Shrock <shrockc@inhs.org>
 */

/**
 * Update the configuration parameters of the
 * module given the information passed back by the modification form
 */
function authurl_admin_updateconfig()
{
    # Get parameters
    if (!xarVarFetch('adduser',      'checkbox', $adduser,      false, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('activate',     'checkbox', $activate,     false, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('authurl',      'str:1:',   $authurl,      '',    XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('debuglevel',   'str:1:',   $debuglevel,   'None',    XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('defaultgroup', 'str:1:',   $defaultgroup, '',    XARVAR_NOT_REQUIRED)) return;


    # Confirm authorization code
    if (!xarSecConfirmAuthKey()) return;

    if(!$adduser){
        xarModSetVar('authurl', 'add_user', 'false');
    } else {
        xarModSetVar('authurl', 'add_user', 'true');
    }// if

    xarModSetVar('authurl', 'auth_url', $authurl);
    xarModSetVar('authurl', 'debug_level', $debuglevel);
    xarModSetVar('authurl', 'default_group', $defaultgroup);

    $authmodules = xarConfigGetVar('Site.User.AuthenticationModules');
    if (empty($activate) && in_array('authurl', $authmodules)) {
        $newauth = array();
        foreach ($authmodules as $module) {
            if ($module != 'authurl') {
                $newauth[] = $module;
            }
        }
        xarConfigSetVar('Site.User.AuthenticationModules', $newauth);
    } elseif (!empty($activate) && !in_array('authurl', $authmodules)) {
        $newauth = array();
        foreach ($authmodules as $module) {
            if ($module == 'authsystem') {
                $newauth[] = 'authurl';
            }
            $newauth[] = $module;
        }
        xarConfigSetVar('Site.User.AuthenticationModules', $newauth);
    }

    # lets update status and display updated configuration
    xarResponseRedirect(xarModURL('authurl', 'admin', 'modifyconfig'));

    # Return
    return true;
}

?>
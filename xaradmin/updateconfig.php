<?php
/**
 * File: $Id$
 *
 * AuthURL Administrative Display Functions
 *
 * @package authentication
 * @copyright (C) 2002 by the Xaraya Development Team.
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.com
 *
 * @subpackage authurl
 * @author Court Shrock <shrockc@inhs.org>
*/

/**
 * Update the configuration parameters of the
 * module given the information passed back by the modification form
 */
function authurl_admin_updateconfig()
{
    # Get parameters

    list($adduser,
         $activate,
         $authurl,
         $debuglevel,
         $defaultgroup ) = xarVarCleanFromInput('adduser',
                                                'activate',
                                                'authurl',
                                                'debuglevel',
                                                'defaultgroup');

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

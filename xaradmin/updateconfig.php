<?php
/**
 * File: $Id$
 *
 * Administrator update config
 *
 * @package authentication
 * @copyright (C) 2002 by the Xaraya Development Team.
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.org
 *
 * @subpackage authinvision2
 * @author Brian McCloskey <brian@nexusden.com>
*/
function authinvision2_admin_updateconfig()
{
    // Get parameters
    
    list($server,
        $database,
        $username,
        $password,
        $prefix,
        $forumroot,
        $defaultgroup,
        $onlyauth, ) = xarVarCleanFromInput('server',
                                              'database', 
                                              'username', 
                                              'password', 
                                              'prefix', 
                                              'forumroot', 
                                              'defaultgroup',
                                              'onlyauth');

    // Confirm authorisation code
    if (!xarSecConfirmAuthKey()) return;

    if ($onlyauth == 1) {
        xarModSetVar('authinvision2', 'onlyauth', $onlyauth);
        $authModules = xarConfigGetVar('Site.User.AuthenticationModules');
        $authModulesUpdate = array();
        // Loop through current auth modules and remove all but 'authinvision2'
        foreach ($authModules as $authType) {
            if ($authType == 'authinvision2')
                $authModulesUpdate[] = $authType;
        }
        xarConfigSetVar('Site.User.AuthenticationModules',$authModulesUpdate);
    }

    xarModSetVar('authinvision2', 'server', $server);
    xarModSetVar('authinvision2', 'database', $database);
    xarModSetVar('authinvision2', 'username', $username);
    xarModSetVar('authinvision2', 'password', $password);
    xarModSetVar('authinvision2', 'prefix', $prefix);
    xarModSetVar('authinvision2', 'forumroot', $forumroot);

    // Get default users group
    if (!isset($defaultgroup)) {
        // See if Users role exists
        if( xarFindRole("Users"))
            $defaultgroup = 'Users';
    } 
    xarModSetVar('authinvision2', 'defaultgroup', $defaultgroup);

    // lets update status and display updated configuration
    xarResponseRedirect(xarModURL('authinvision2', 'admin', 'modifyconfig'));

    // Return
    return true;
}
?>

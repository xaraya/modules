<?php
/**
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
    if(!xarVarFetch('server'      ,'str:1',$server      ,''  , XARVAR_NOT_REQUIRED)) {return;}
    if(!xarVarFetch('database'    ,'str:1',$database    ,''  , XARVAR_NOT_REQUIRED)) {return;}
    if(!xarVarFetch('username'    ,'str:1',$username    ,''  , XARVAR_NOT_REQUIRED)) {return;}
    if(!xarVarFetch('password'    ,'str:1',$password    ,''  , XARVAR_NOT_REQUIRED)) {return;}
    if(!xarVarFetch('prefix'      ,'str:1',$prefix      ,''  , XARVAR_NOT_REQUIRED)) {return;}
    if(!xarVarFetch('forumroot'   ,'str:1',$forumroot   ,''  , XARVAR_NOT_REQUIRED)) {return;}
    if(!xarVarFetch('defaultgroup','isset',$defaultgroup,NULL, XARVAR_DONT_SET     )) {return;}
    if(!xarVarFetch('onlyauth'    ,'int:1',$onlyauth    ,0   , XARVAR_NOT_REQUIRED)) {return;}

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

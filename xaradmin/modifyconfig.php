<?php
/**
 * Modify module's configuration
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Example Module
 * @link http://xaraya.com/index.php/release/36.html
 * @author Example Module Development Team
 */

/**
 * Modify module's configuration
 *
 * This is a standard function to modify the configuration parameters of the
 * module
 *
 * @author Example module development team
 * @return array
 */
function twitter_admin_modifyconfig()
{ 

    if (!xarSecurityCheck('AdminTwitter')) return;

    if (!xarVarFetch('phase', 'isset', $phase, 'form', XARVAR_NOT_REQUIRED)) return;

    /* Generate a one-time authorisation code for this operation */
    $data['authid'] = xarSecGenAuthKey();

    switch ($phase) {
      case 'form':
      default:
        /* Specify some values for display */
        $data['username'] = xarModGetVar('twitter', 'username');
        $data['password'] = xarModGetVar('twitter', 'password');
        $data['owner']    = xarModGetVar('twitter', 'owner');
        $data['shorturls'] = xarModGetVar('twitter', 'SupportShortURLs');
        $data['usealias'] = xarModGetVar('twitter', 'useModuleAlias');
        $data['aliasname']= xarModGetVar('twitter','aliasname');
        $data['itemsperpage'] = xarModGetVar('twitter', 'itemsperpage');

        $hooks = xarModCallHooks('module', 'modifyconfig', 'twitter',
                           array('module' => 'twitter'));
        $data['hooks'] = $hooks;
        $data['hookoutput'] = $hooks;
      break;
      case 'update':
        if (!xarVarFetch('username', 'str:1', $username, '', XARVAR_NOT_REQUIRED)) return;
        if (!xarVarFetch('password', 'str:1', $password, '', XARVAR_NOT_REQUIRED)) return;
        if (!xarVarFetch('shorturls', 'checkbox', $shorturls, false, XARVAR_NOT_REQUIRED)) return;
        if (!xarVarFetch('usealias', 'checkbox', $usealias, false, XARVAR_NOT_REQUIRED)) return;
        if (!xarVarFetch('aliasname', 'str:1', $aliasname, '', XARVAR_NOT_REQUIRED)) return;
        if (!xarVarFetch('itemsperpage', 'int:1', $itemsperpage, 20, XARVAR_NOT_REQUIRED)) return;
        if (!xarVarFetch('owner', 'id', $owner, xarModGetVar('roles', 'admin'), XARVAR_NOT_REQUIRED)) return;

        xarModSetVar('twitter', 'username', $username);
        xarModSetVar('twitter', 'password', $password);
        xarModSetVar('twitter', 'useModuleAlias', $usealias);
        xarModSetVar('twitter', 'aliasname', $aliasname);
        xarModSetVar('twitter', 'SupportShortURLs', $shorturls);
        xarModSetVar('twitter', 'itemsperpage', $itemsperpage);    
        xarModSetVar('twitter', 'owner', $owner);

        if (!xarSecConfirmAuthKey()) return;

        xarResponseRedirect(xarModURL('twitter', 'admin', 'modifyconfig'));
        return true;
      break;
    }
    return $data;
}
?>

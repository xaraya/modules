<?php
// File: $Id$
/*
 * Xaraya Multisites
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.org
 *
 * @subpackage Multisites Module
 * @author
 */

function multisites_admin_updateconfig($args)
{
    global $HTTP_SERVER_VARS;

    extract($args);

    if (!xarVarFetch('servervar', 'str:9:', $servervar, 'HTTP_HOST')) return;
    if (!xarVarFetch('themepath', 'str:2:', $themepath, 'themes')) return;
    if (!xarVarFetch('varpath', 'str:2:', $varpath, 'var')) return;
    if (!xarVarFetch('masterfolder', 'str:4:', $masterfolder, 'xarsites')) return;
    if (!xarVarFetch('DNexts', 'str', $DNexts, '.com,.org,.net')) return;
 
    // Auth Key
    if (!xarSecConfirmAuthKey()) return;

    // Security
    if (!xarSecurityCheck('AdminMultisites')) {
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'NO_PERMISSION');
        return;
    }

    // If Master created, we don't want to set the system Master and $masterurl again
    // Just update mod vars
    xarModSetVar('multisites', 'servervar', $servervar);
    // Hmmm, needs more thought.
    //<jojodee> setting anyway for now, but don't think we want this $themepath and $varpath to be changeable
    xarModSetVar('multisites', 'themepath', $themepath);
    xarModSetVar('multisites', 'varpath', $varpath);
    xarModSetVar('multisites', 'masterfolder', $masterfolder);
    xarModSetVar('multisites', 'DNexts', $DNexts);
    xarConfigSetVar('System.DB.TablePrefix',xarDBGetSystemTablePrefix());
    xarConfigSetVar('Site.DB.TablePrefix',xarDBGetSiteTablePrefix());

   //Check the master site directory exists
    $var = is_dir($masterfolder);
    if ($var != true) {
        $msg = xarML("You master site directory \"".$masterfolder."\" does not exist! Please create it first and make sure it is writeable chmod 777!.");
            xarExceptionSet(XAR_USER_EXCEPTION, 'MISSING_DIRECTORY', new DefaultUserException($msg));
            return $msg;
    }

    $setconfig = xarModFunc('multisites',
                            'admin',
                            'setconfig',
                            array('masterfolder' => $masterfolder,
                                  'servervar'  => $servervar,
                                  'DNexts'     => $DNexts));

    if (!$setconfig) {
        $msg = xarML('Unable to configure Master Multisite, check all directories exist and are writeable, and database tables are available.');
        xarExceptionSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
       return false;
    }

   xarResponseRedirect(xarModURL('multisites', 'admin', 'view'));

    return true;
}
?>

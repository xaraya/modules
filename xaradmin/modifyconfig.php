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

/**
 * The standard overview screen on entry to the Multisite module.
 * @ returns output
 * @return output with Multisite Overview and Menu information
 */

function multisites_admin_modifyconfig()
{
    global $HTTP_SERVER_VARS;

    // Security check
    if (!xarSecurityCheck('AdminMultisites')) {
           return;
    }

    $lIsMultisites = xarConfigGetVar('System.MS.MultiSites');
    $lIsMaster=xarConfigGetVar('System.MS.Master');
    $masterurl=xarModGetVar('multisites','masterurl');
    $servervar=xarModGetVar('multisites','servervar');
    $currenthost=$_SERVER[$servervar];
     if (($lIsMultisites==1) and ($lIsMaster==1) and ($currenthost==$masterurl)){
    // The master multisite has been configured and this is the master - continue

        $data['modifysite']=1;

    } else {
        $data['modifysite']=0;
    }
        $data['authid'] = xarSecGenAuthKey();

        $data['SERVER_NAME'] = $HTTP_SERVER_VARS['SERVER_NAME'];
        $data['HTTP_HOST']   = $HTTP_SERVER_VARS['HTTP_HOST'];
        $data['masterfolder']  = xarModGetVar('multisites','masterfolder');
        $data['DNexts']      = xarModGetVar('multisites','DNexts');

    // Return the template variables defined in this function
    return $data;
}
?>

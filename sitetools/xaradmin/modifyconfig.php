<?php
/*
 * File: $Id: modifyconfig.php,v 1.1 2003/09/19 09:16:03 jojodee Exp $
 *
 * SiteTools Module
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by Jo Dalle Nogare
 * @link http://xaraya.athomeandabout.com
 *
 * @subpackage SiteTools module
 * @author jojodee <http://xaraya.athomeandabout.com >
*/

/**
 * This is a standard function to modify the configuration parameters of the
 * module
 */
function sitetools_admin_modifyconfig()
{ 
    // Initialise the $data variable that will hold the data to be used in
    // the blocklayout template, and get the common menu configuration
    $data = xarModAPIFunc('sitetools', 'admin', 'menu');
    // Security check - important to do this as early as possible
    if (!xarSecurityCheck('AdminSiteTools')) return;
    // Generate a one-time authorisation code for this operation
    $data['authid'] = xarSecGenAuthKey(); 
    // Specify some labels and values for display
    $data['adopath']     = xarModGetVar('sitetools','adocachepath');
    $data['rsspath']     = xarModGetVar('sitetools','rsscachepath');
    $data['templpath']   = xarModGetVar('sitetools','templcachepath');
    $data['backuppath']  = xarModGetVar('sitetools','backuppath');
    $data['usetimestamp']= xarModGetVar('sitetools','timestamp');
    $data['lineterm']    = xarModGetVar('sitetools','lineterm');
    $data['colnumber']    = xarModGetVar('sitetools','colnumber');    
    $data['updatebutton']= xarVarPrepForDisplay(xarML('Update Configuration'));

    $data['defadopath']   = xarCoreGetVarDirPath()."/cache/adodb";
    $data['defrsspath']   = xarCoreGetVarDirPath()."/cache/rss";
    $data['deftemplpath'] = xarCoreGetVarDirPath()."/cache/templates";

    $hooks = xarModCallHooks('module', 'modifyconfig', 'sitetools',
        array('module' => 'sitetools'));
    if (empty($hooks)) {
        $data['hooks'] = '';
    } elseif (is_array($hooks)) {
        $data['hooks'] = join('', $hooks);
    } else {
        $data['hooks'] = $hooks;
    } 
    // Return the template variables defined in this function
    return $data;
}
?>

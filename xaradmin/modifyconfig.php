<?php
/*
 * File: $Id: $
 *
 * Newsletter 
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003-2004 by the Xaraya Development Team
 * @link http://www.xaraya.com
 *
 * @subpackage newsletter module
 * @author Richard Cave <rcave@xaraya.com>
*/


/**
 * Modify configuration
 *
 * @author Richard Cave
 * @returns array
 * @return $data
 */
function newsletter_admin_modifyconfig()
{
    // Security check
    if(!xarSecurityCheck('AdminNewsletter')) return;

    // Get the admin edit menu
    $data['menu'] = xarModFunc('newsletter', 'admin', 'configmenu');

    // Generate a one-time authorisation code for this operation
    $data['authid'] = xarSecGenAuthKey();

    // Specify buttons
    $data['updatebutton'] = xarVarPrepForDisplay(xarML('Update Configuration'));

    // Set hooks
    $hooks = xarModCallHooks('module', 
                             'modifyconfig', 
                             'newsletter',
                             array('module' => 'newsletter'));

    if (empty($hooks) || !is_string($hooks)) {
        $data['hooks'] = '';
    } else {
        $data['hooks'] = $hooks;
    }

    // Provide encode/decode functions forshort URLs 
    $data['bulkemail'] = xarModGetVar('newsletter','bulkemail') ? 'checked' : '';
    $data['shorturlschecked'] = xarModGetVar('newsletter','SupportShortURLs') ? 'checked' : '';
    $data['activeuserschecked'] = xarModGetVar('newsletter','activeusers') ? 'checked' : '';

    // Return the template variables defined in this function
    return $data;
}

?>

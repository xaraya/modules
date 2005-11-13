<?php
/**
 * File: $Id:
 * 
 * Standard function to modify configuration parameters
 * 
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Sigmapersonnel Module
 * @author Michel V. 
 */
/**
 * This is a standard function to modify the configuration parameters of the
 * module
 */
function sigmapersonnel_admin_modifyconfig()
{ 
    // Initialise the $data variable
    $data = xarModAPIFunc('sigmapersonnel', 'admin', 'menu'); 

    if (!xarSecurityCheck('AdminSIGMAPersonnel')) return; 
    
    // Generate a one-time authorisation code for this operation
    $data['authid'] = xarSecGenAuthKey(); 
    // Specify some labels and values for display
    $data['boldlabel'] = xarVarPrepForDisplay(xarML('Display Example Items In Bold?'));
    $data['boldchecked'] = xarModGetVar('sigmapersonnel', 'bold') ? true : false;
    $data['itemslabel'] = xarVarPrepForDisplay(xarML('Items Per Page?'));
    $data['itemsvalue'] = xarModGetVar('sigmapersonnel', 'itemsperpage');
    $data['updatebutton'] = xarVarPrepForDisplay(xarML('Update Configuration')); 
    // Note : if you don't plan on providing encode/decode functions for
    // short URLs (see xaruserapi.php), you should remove these from your
    // admin-modifyconfig.xard template !
    $data['shorturlslabel'] = xarML('Enable short URLs?');
    $data['shorturlschecked'] = xarModGetVar('sigmapersonnel', 'SupportShortURLs') ? true : false;

    $hooks = xarModCallHooks('module', 'modifyconfig', 'sigmapersonnel',
        array('module' => 'sigmapersonnel'));
    if (empty($hooks)) {
        $data['hookoutput'] = '';
    } else {
        // You can use the output from individual hooks in your template too, e.g. with
        // $hookoutput['categories'], $hookoutput['dynamicdata'], $hookoutput['keywords'] etc.
        $data['hookoutput'] = $hooks;
    } 
    // Return the template variables defined in this function
    return $data;
} 

?>

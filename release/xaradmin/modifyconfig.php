<?php
/**
 * File: $Id:
 * 
 * Standard function to modify configuration parameters
 * 
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage release
 * @author Release module development team 
 */
/**
 * This is a standard function to modify the configuration parameters of the
 * module
 */
function release_admin_modifyconfig()
{ 
    // Security check
    if (!xarSecurityCheck('AdminRelease')) return; 

    // Generate a one-time authorisation code for this operation
    $data['authid'] = xarSecGenAuthKey(); 

    // Specify some labels and values for display
    $data['updatebutton'] = xarVarPrepForDisplay(xarML('Update Configuration')); 

    $data['shorturlslabel'] = xarML('Enable short URLs?');
    $data['shorturlschecked'] = xarModGetVar('release', 'SupportShortURLs') ? 'checked' : '';

    $hooks = xarModCallHooks('module', 'modifyconfig', 'release',
        array('module' => 'release'));
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
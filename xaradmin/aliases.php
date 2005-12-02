<?php
/**
 * File: $Id:
 * 
 * Manage book aliases
 * 
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage bible
 * @author curtisdf 
 */
/**
 * manage aliases
 */
function bible_admin_aliases()
{
    if (!xarSecurityCheck('EditBible')) return; 

    $data = xarModAPIFunc('bible', 'admin', 'menu'); 

    // Specify some labels for display
    $data['aidlabel'] = xarML('ID');
    $data['swordlabel'] = xarML('DB Name');
    $data['displaylabel'] = xarML('Display Name');
    $data['aliaseslabel'] = xarML('Aliases');

    $data['updatebutton'] = xarML('Update Aliases');

    // get alias data
    $aliases = xarModAPIFunc('bible', 'user', 'getaliases');

    // Check for exceptions
    if (!isset($aliases) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; // throw back

    // send aliases to template
    $data['aliases'] = $aliases;

    // Return the template variables defined in this function
    return $data; 

} 

?>

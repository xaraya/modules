<?php
/**
 * File: $Id:
 * 
 * Standard function to create a new module item
 * 
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage userpoints
 * @author Userpoints module development team 
 */
/**
 * add new item
 * This is a standard function that is called whenever an administrator
 * wishes to create a new module item
 */
function userpoints_admin_newrank($args)
{ 
    // Admin functions of this type can be called by other modules.  If this
    // happens then the calling module will be able to pass in arguments to
    // this function through the $args parameter.  Hence we extract these
    // arguments *before* we have obtained any form-based input through
    // xarVarFetch().
    extract($args);

    // Get parameters from whatever input we need.  All arguments to this
    // function should be obtained from xarVarFetch(), xarVarCleanFromInput()
    // is a degraded function.  xarVarFetch allows the checking of the input
    // variables as well as setting default values if needed.  Getting vars
    // from other places such as the environment is not allowed, as that makes
    // assumptions that will not hold in future versions of Xaraya
    if (!xarVarFetch('rankname', 'str:1:', $rankname, $rankname, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('rankminscore', 'str:1:', $rankminscore, $rankminscore,XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('invalid', 'str:1:', $invalid, $invalid, XARVAR_NOT_REQUIRED)) return; 
    // Initialise the $data variable that will hold the data to be used in
    // the blocklayout template, and get the common menu configuration - it
    // helps if all of the module pages have a standard menu at the top to
    // support easy navigation
    // Security check - important to do this as early as possible to avoid
    // potential security holes or just too much wasted processing
    if (!xarSecurityCheck('AddRank')) return; 
    // Generate a one-time authorisation code for this operation
    $data['authid'] = xarSecGenAuthKey();
    $data['invalid'] = $invalid; 
    // Specify some labels for display
    $data['ranknamelabel'] = xarVarPrepForDisplay(xarML('Rank Name'));
    $data['rankminscorelabel'] = xarVarPrepForDisplay(xarML('Rank Minimum Score'));
    $data['addbutton'] = xarVarPrepForDisplay(xarML('Add Rank'));

    $item = array();
    $item['module'] = 'userpoints';
    $hooks = xarModCallHooks('item', 'new', '', $item);

    if (empty($hooks)) {
        $data['hooks'] = '';
    } elseif (is_array($hooks)) {
        $data['hooks'] = join('', $hooks);
    } else {
        $data['hooks'] = $hooks;
    } 
    // For E_ALL purposes, we need to check to make sure the vars are set.
    // If they are not set, then we need to set them empty to surpress errors
    if (empty($rankname)) {
        $data['rankname'] = '';
    } else {
        $data['rankname'] = $rankname;
    } 

    if (empty($rankminscore)) {
        $data['rankminscore'] = '';
    } else {
        $data['rankminscore'] = $rankminscore;
    } 
    // Return the template variables defined in this function
    return $data;
} 

?>

<?php
/**
 * Make a new rank
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Userpoints module
 * @link http://xaraya.com/index.php/release/782.html
 * @author Userpoints module development team
 */
/**
 * add new rank
 * This is a standard function that is called whenever an administrator
 * wishes to create a new module item
 */
function userpoints_admin_newrank($args)
{
    extract($args);

    // Get parameters from whatever input we need.
    if (!xarVarFetch('rankname', 'str:1:', $rankname, $rankname, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('rankminscore', 'str:1:', $rankminscore, $rankminscore,XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('invalid', 'isset', $invalid, $invalid, XARVAR_NOT_REQUIRED)) return;

    // Security check - important to do this as early as possible to avoid
    // potential security holes or just too much wasted processing
    if (!xarSecurityCheck('AddUserpointsRank')) return;
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

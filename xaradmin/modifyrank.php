<?php
/**
 * Modify a rank
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Userpoints Module
 * @link http://xaraya.com/index.php/release/782.html
 * @author Userpoints Module Development Team
 */
/**
 * modify a rank
 * This is a standard function that is called whenever an administrator
 * wishes to modify a current module item
 *
 * @param id $id the id of the item to be modified
 */
function userpoints_admin_modifyrank($args)
{
    extract($args);
    // Get parameters from whatever input we need.
    if (!xarVarFetch('id', 'id', $id)) return;
    if (!xarVarFetch('invalid', 'isset', $invalid, $invalid, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('rankname', 'str:1:', $rankname, $rankname, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('rankminscore', 'str:1:', $rankminscore, $rankminscore,XARVAR_NOT_REQUIRED)) return;

    // The user API function is called.  This takes the item ID which we
    // obtained from the input and gets us the information on the appropriate
    // item.  If the item does not exist we post an appropriate message and
    // return
    $item = xarModAPIFunc('userpoints',
                          'user',
                          'getrank',
                          array('id' => $id));
    // Check for exceptions
    if (!isset($item) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; // throw back

    // Security check
    if (!xarSecurityCheck('EditUserpointsRank', 1, 'Rank', "$item[rankname]:$id")) {
        return;
    }
    // Get menu variables - it helps if all of the module pages have a standard
    // menu at their head to aid in navigation
    // $menu = xarModAPIFunc('example','admin','menu','modify');
    $item['module'] = 'userpoints';
    $hooks = xarModCallHooks('item', 'modify', $id, $item);
    if (empty($hooks)) {
        $hooks = '';
    } elseif (is_array($hooks)) {
        $hooks = join('', $hooks);
    }
    // Return the template variables defined in this function
    return array('authid'       => xarSecGenAuthKey(),
                 'ranknamelabel'    => xarVarPrepForDisplay(xarML('Rank Name')),
                 'rankname'         => $rankname,
                 'rankminscorelabel'  => xarVarPrepForDisplay(xarML('Rank Minimum Score')),
                 'rankminscore'       => $rankminscore,
                 'invalid'      => $invalid,
                 'updatebutton' => xarVarPrepForDisplay(xarML('Update')),
                 'hooks'        => $hooks,
                 'item'         => $item);
}

?>

<?php
/**
 * File: $Id:
 * 
 * Standard function to modify an item
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
 * modify an item
 * This is a standard function that is called whenever an administrator
 * wishes to modify a current module item
 * 
 * @param  $ 'exid' the id of the item to be modified
 */
function userpoints_admin_modifyrank($args)
{ 

    // Admin functions of this type can be called by other modules.  If this
    // happens then the calling module will be able to pass in arguments to
    // this function through the $args parameter.  Hence we extract these
    // arguments *before* we have obtained any form-based input through
    // xarVarFetch(), so that parameters passed by the modules can also be
    // checked by a certain validation.
    extract($args);

    // Get parameters from whatever input we need.  All arguments to this
    // function should be obtained from xarVarFetch(),
    // xarVarFetch allows the checking of the input
    // variables as well as setting default values if needed.  Getting vars
    // from other places such as the environment is not allowed, as that makes
    // assumptions that will not hold in future versions of Xaraya
    if (!xarVarFetch('id', 'int:1:', $id)) return;
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
     
    // Security check - important to do this as early as possible to avoid
    // potential security holes or just too much wasted processing.  However,
    // in this case we had to wait until we could obtain the item name to
    // complete the instance information so this is the first chance we get to
    // do the check
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

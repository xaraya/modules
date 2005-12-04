<?php
/**
 * Standard function to create a rank
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
 * This is a standard function that is called with the results of the
 * form supplied by xarModFunc('example','admin','new') to create a new item
 *
 * @param  $ 'name' the name of the item to be created
 * @param  $ 'number' the number of the item to be created
 */
function userpoints_admin_createrank($args)
{
    extract($args);
    // Get parameters from whatever input we need.
    if (!xarVarFetch('rankname', 'str:1:', $rankname, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('rankminscore', 'int:0:', $rankminscore, '',XARVAR_NOT_REQUIRED)) return;

    // Argument check
    $item = xarModAPIFunc('userpoints',
                          'admin',
                          'validaterank',
                          array('rankname' => $rankname));

    // Argument check
    $invalid = array();
    if (!is_numeric($rankminscore) || $rankminscore < 0) {
        $invalid['rankminscore'] = 1;
        $rankminscore = '';
    }
    if (empty($rankname) || !is_string($rankname)) {
        $invalid['rankname'] = 1;
        $rankname = '';
    }

    if (!empty($rankname) && $item['rankname'] == $rankname) {
        $invalid['duplicatename'] = 1;
        $duplicatename = '';
    }
    // check if we have any errors
    if (count($invalid) > 0) {
        // call the admin_new function and return the template vars
        // (you need to copy admin-new.xd to admin-create.xd here)
        return xarModFunc('userpoints', 'admin', 'newrank',
                          array('rankname' => $rankname,
                                'rankminscore' => $rankminscore,
                                'invalid' => $invalid));
    }
    // Confirm authorisation code.
    if (!xarSecConfirmAuthKey()) return;
    // The API function is called.  Note that the name of the API function and
    // the name of this function are identical, this helps a lot when
    // programming more complex modules.  The arguments to the function are
    // passed in as their own arguments array
    $id = xarModAPIFunc('userpoints',
                        'admin',
                        'createrank',
                          array('rankname' => $rankname,
                                'rankminscore' => $rankminscore));
    // The return value of the function is checked here,.
    if (!isset($id) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; // throw back
    // This function generated no output, and so now it is complete we redirect
    // the user to an appropriate page for them to carry on their work
    xarResponseRedirect(xarModURL('userpoints', 'admin', 'viewrank'));
    // Return
    return true;
}

?>

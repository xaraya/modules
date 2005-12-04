<?php
/**
 * Update rank
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
 * This is a standard function that is called with the results of the
 * form supplied by xarModFunc('example','admin','modify') to update a current item
 *
 * @param id $id the id of the rank to be updated
 * @param str $ 'rankname' the name of the item to be updated
 * @param int $ 'rankminscore' the number of the item to be updated
 */
function userpoints_admin_updaterank($args)
{
    extract($args);

    // Get parameters from whatever input we need.  All arguments to this
    // function should be obtained from xarVarFetch(),
    // xarVarFetch allows the checking of the input
    // variables as well as setting default values if needed.  Getting vars
    // from other places such as the environment is not allowed, as that makes
    // assumptions that will not hold in future versions of Xaraya
    if (!xarVarFetch('id', 'id', $id, $id, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('rankname', 'str:1:', $rankname, $rankname, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('rankminscore', 'int:0:', $rankminscore, $rankminscore, XARVAR_NOT_REQUIRED)) return;

    // Confirm authorisation code.
    if (!xarSecConfirmAuthKey()) return;
    // Notable by its absence there is no security check here.  This is because
    // the security check is carried out within the API function and as such we
    // do not duplicate the work here

    $invalid = array();
    if (!is_numeric($rankminscore) || $rankminscore < 0) {
        $invalid['rankminscore'] = 1;
        $rankminscore = '';
    }
    if (empty($rankname) || !is_string($rankname)) {
        $invalid['rankname'] = 1;
        $rankname = '';
    }

    // check if we have any errors
    if (count($invalid) > 0) {
        // call the admin_new function and return the template vars
        // (you need to copy admin-new.xd to admin-create.xd here)
        return xarModFunc('userpoints', 'admin', 'modifyrank',
                          array('id'     => $id,
                                'rankname'     => $rankname,
                                'rankminscore'   => $rankminscore,
                                'invalid'  => $invalid));
    }

    if (!xarModAPIFunc('userpoints',
                       'admin',
                       'updaterank',
                       array('id'   => $id,
                             'rankname'   => $rankname,
                             'rankminscore' => $rankminscore))) {
        return; // throw back
    }
    xarSessionSetVar('statusmsg', xarML('Rank Was Successfully Updated!'));
    // This function generated no output, and so now it is complete we redirect
    // the user to an appropriate page for them to carry on their work
    xarResponseRedirect(xarModURL('userpoints', 'admin', 'viewrank'));
    // Return
    return true;
}

?>

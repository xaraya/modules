<?php
/**
 * Create a new item
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Example Module
 * @link http://xaraya.com/index.php/release/36.html
 * @author Example Module Development Team
 */

/**
 * Create a new item
 *
 * Standard function to create a new item
 * This is a standard function that is called with the results of the
 * form supplied by xarModFunc('example','admin','new') to create a new item
 *
 * @author MichelV
 * @param  $ 'name' the name of the item to be created
 * @param  $ 'number' the number of the item to be created
 */
function courses_admin_createtype($args)
{
    extract($args);

    if (!xarVarFetch('tid',     'id',     $tid,     $tid, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('objectid', 'id',     $objectid, $objectid, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('invalid',  'str:1:', $invalid,  '', XARVAR_NOT_REQUIRED)) return;
//    if (!xarVarFetch('number',   'int:1:', $number,   $number, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('coursetype',     'str:1:', $coursetype,     '', XARVAR_NOT_REQUIRED)) return;

    // Argument check
    $invalid = array();
    if (empty($coursetype) || !is_string($coursetype)) {
        $invalid['coursetype'] = 1;
        $name = '';
    }

    // check if we have any errors
    if (count($invalid) > 0) {
        /* If we get here, we have encountered errors.
         * Send the user back to the admin_new form
         * call the admin_new function and return the template vars
         */
        return xarModFunc('courses', 'admin', 'newtype',
                          array('coursetype' => $coursetype,
                                'invalid' => $invalid));
    }
    if (!xarSecConfirmAuthKey()) return;
    $tid = xarModAPIFunc('courses',
                          'admin',
                          'createtype',
                          array('coursetype' => $coursetype));
    /* The return value of the function is checked here, and if the function
     * suceeded then an appropriate message is posted. Note that if the
     * function did not succeed then the API function should have already
     * posted a failure message so no action is required
     */
    if (!isset($tid) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; // throw back
    /* This function generated no output, and so now it is complete we redirect
     * the user to an appropriate page for them to carry on their work
     */
    xarResponseRedirect(xarModURL('courses', 'admin', 'viewtypes'));
    /* Return true, in this case */
    return true;
}
?>

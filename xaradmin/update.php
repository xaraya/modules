<?php
/**
 * Standard function to update a current item
 *
 * @package modules
 * @copyright (C) 2002-2009 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Example Module
 * @link http://xaraya.com/index.php/release/36.html
 * @author Example Module Development Team
 */
/**
 * Standard function to update a current item
 *
 * This function is called with the results of the
 * form supplied by xarModFunc('example','admin','modify') to update a current item
 *
 * @author Example module development team
 * @param  int    $args['exid']  the id of the item to be updated
 * @param  string $args['name']  the name of the item to be updated
 * @param  int    $args['number'] the number of the item to be updated
 */
function example_admin_update($args)
{
    /* See modify.php for information on passed in arguments
     */
    extract($args);

    /* See modify.php for information on getting parameters from input
     */
    if (!xarVarFetch('exid',     'id',     $exid,     $exid,     XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('objectid', 'id',     $objectid, $objectid, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('invalid',  'array',  $invalid,  $invalid,        XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('number',   'int:1:', $number,   $number,   XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('name',     'str:1:', $name,     $name,     XARVAR_NOT_REQUIRED)) return;

    /* See modify.php for information on the use of $exid and $objectid
     */
    if (!empty($objectid)) {
        $exid = $objectid;
    }

    /* Confirm authorisation code. This checks that the form had a valid
     * authorisation code attached to it. If it did not then the function will
     * proceed no further as it is possible that this is an attempt at sending
     * in false data to the system
     */
    if (!xarSecConfirmAuthKey()) return;
    /* Notable by its absence there is no security check here. This is because
     * the security check is carried out within the API function and as such we
     * do not duplicate the work here
     */

    $invalid = array();
    if (empty($number) || !is_numeric($number)) {
        $invalid['number'] = 1;
        $number = '';
    }
    if (empty($name) || !is_string($name)) {
        $invalid['name'] = 1;
        $name = '';
    }

    /* check if we have any errors */
    if (count($invalid) > 0) {
        /* We got an error. So call the admin_modify function and return 
         * the template vars
         */
        return xarModFunc('example', 'admin', 'modify',
                          array('name'     => $name,
                                'number'   => $number,
                                'invalid'  => $invalid));
    }

    /* The API function is called. Note that the name of the API function and
     * the name of this function are identical, this helps a lot when
     * programming more complex modules. The arguments to the function are
     * passed in as their own arguments array.
     *
     * The return value of the function is checked here, and if the function
     * suceeded then an appropriate message is posted. Note that if the
     * function did not succeed then the API function should have already
     * posted a failure message so no action is required
     */
    if (!xarModAPIFunc('example',
                       'admin',
                       'update',
                       array('exid'   => $exid,
                             'name'   => $name,
                             'number' => $number))) {
        return; /* throw back */
    }
    xarSessionSetVar('statusmsg', xarML('Example Item was successfully updated!'));
    /* This function generated no output, and so now it is complete we redirect
     * the user to an appropriate page for them to carry on their work
     */
    xarResponseRedirect(xarModURL('example', 'admin', 'view'));

    return true;
}
?>
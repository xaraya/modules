<?php
/**
 * Standard function to update a current item
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
 * Standard function to update a current item
 *
 * This function is called with the results of the
 * form supplied by xarModFunc('courses','admin','modify') to update a current item
 *
 * @author Example module development team
 * @param  $ 'exid' the id of the item to be updated
 * @param  $ 'name' the name of the item to be updated
 * @param  $ 'number' the number of the item to be updated
 */
function courses_admin_updatetype($args)
{
    extract($args);

    if (!xarVarFetch('tid',     'id',     $tid,     $tid,     XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('objectid', 'id',     $objectid, $objectid, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('invalid',  'array',  $invalid,  $invalid,        XARVAR_NOT_REQUIRED)) return;
//    if (!xarVarFetch('number',   'int:1:', $number,   $number,   XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('coursetype',     'str:1:', $coursetype,     $coursetype,     XARVAR_NOT_REQUIRED)) return;

    if (!empty($objectid)) {
        $tid = $objectid;
    }

    /* Confirm authorisation code. This checks that the form had a valid
     */
    if (!xarSecConfirmAuthKey()) return;

    $invalid = array();
    if (empty($coursetype) || !is_string($coursetype)) {
        $invalid['coursetype'] = 1;
        $name = '';
    }

    /* check if we have any errors */
    if (count($invalid) > 0) {
        /* call the admin_new function and return the template vars
         * (you need to copy admin-new.xd to admin-create.xd here)
         */
        return xarModFunc('courses', 'admin', 'modifytype',
                          array('coursetype'     => $coursetype,
                                'invalid'  => $invalid));
    }

    /* The API function is called. Note that the name of the API function and

     */
    if (!xarModAPIFunc('courses',
                       'admin',
                       'updatetype',
                       array('tid'   => $tid,
                             'coursetype'   => $coursetype))) {
        return; /* throw back */
    }
    // Call updateconfig hooks with module + itemtype
    xarModCallHooks('module','updateconfig','courses',
                    array('module'   => 'courses',
                          'itemtype' => $tid));

    xarSessionSetVar('statusmsg', xarML('Coursetype was successfully updated!'));
    /* This function generated no output, and so now it is complete we redirect
     * the user to an appropriate page for them to carry on their work
     */
    xarResponseRedirect(xarModURL('courses', 'admin', 'modifytype', array('tid' => $tid)));
    /* Return */
    return true;
}
?>
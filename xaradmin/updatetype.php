<?php
/**
 * Standard function to update a current item
 *
 * @package modules
 * @copyright (C) 2005-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Courses Module
 * @link http://xaraya.com/index.php/release/179.html
 * @author Courses module development team
 */
/**
 * Update the coursetype
 *
 * This function is called with the results of the
 * form supplied by xarModFunc('courses','admin','modifytype') to update a current item
 *
 * @author MichelV <michelv@xaraya.com>
 * @param  $ 'tid' the id of the type to be updated
 * @param  $ 'coursetype' the name of the item to be updated
 * @param  $ 'desc' the desciption for this coursetype
 * @param string settings Not in use, collection of settings
 * @param bool allowregi Allow registration in this coursetype
 * @return bool true on success of update and redirect
 */
function courses_admin_updatetype($args)
{
    extract($args);

    if (!xarVarFetch('tid',        'id',        $tid)) return;
    if (!xarVarFetch('objectid',   'id',        $objectid,   $objectid, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('invalid',    'array',     $invalid,    array(),   XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('coursetype', 'str:1:',    $coursetype, '',        XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('descr',      'str:1:255', $descr,      '',        XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('settings',   'str:1:255', $settings,   '',        XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('allowregi',  'checkbox',  $allowregi,  false,     XARVAR_NOT_REQUIRED)) return;

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
                          array('coursetype' => $coursetype,
                                'descr'      => $descr,
                                'settings'   => $settings,
                                'allowregi'  => $allowregi,
                                'invalid'    => $invalid));
    }
    // Update settings
    xarModSetVar('courses', 'allowregi'.$tid, $allowregi);
    /* The API function is called. Note that the name of the API function and  */
    if (!xarModAPIFunc('courses',
                       'admin',
                       'updatetype',
                       array('tid'   => $tid,
                             'coursetype'   => $coursetype,
                             'descr'        => $descr,
                             'settings'     => $settings)
                             )) {
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
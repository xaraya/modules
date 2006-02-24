<?php
/**
 * Create a new item
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
 * Create a new course type
 *
 * Standard function to create a new course type
 * This is a standard function that is called with the results of the
 * form supplied by xarModFunc('courses','admin','newtype') to create a new item
 *
 * @author MichelV <michelv@xaraya.com>
 * @since Dec 2005
 * @param string coursetype
 * @param string descr The desciption of this course type
 * @param string settings
 * @return bool true on success
 */
function courses_admin_createtype($args)
{
    extract($args);

    if (!xarVarFetch('tid',        'id',        $tid,        $tid,      XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('objectid',   'id',        $objectid,   $objectid, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('invalid',    'array',     $invalid,    array(),   XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('coursetype', 'str:1:',    $coursetype, '',        XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('descr',      'str:1:255', $descr,      '',        XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('settings',   'str:1:255', $settings,   '',        XARVAR_NOT_REQUIRED)) return;

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
                                'desc' => $desc,
                                'settings' => $settings,
                                'invalid' => $invalid));
    }
    if (!xarSecConfirmAuthKey()) return;
    $tid = xarModAPIFunc('courses',
                          'admin',
                          'createtype',
                          array('coursetype' => $coursetype,
                                'descr'      => $descr,
                                'settings'   => $settings));
    /* The return value of the function is checked here */
    if (!isset($tid) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; // throw back
    /* This function generated no output, and so now it is complete we redirect
     * the user to an appropriate page for them to carry on their work
     */
    xarResponseRedirect(xarModURL('courses', 'admin', 'viewtypes'));
    /* Return true, in this case */
    return true;
}
?>

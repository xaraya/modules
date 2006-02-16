<?php
/**
 * Create a new maxer
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Maxercalls Module
 * @link http://xaraya.com/index.php/release/247.html
 * @author Maxercalls Module Development Team
 */
/**
 * Create a new maxer
 *
 * Standard function to create a new maxer
 * This is a standard function that is called with the results of the
 * form supplied by xarModFunc('maxercalls','admin','new') to create a new item
 *
 * @author Maxercalls module development team
 * @param array invalid
 * @param int personid
 * @param int ric
 * @param int maxernumber The number to reach the maxer
 * @param int function
 * @param int maxerstatus The status of the maxer
 * @param string program What is this maxer used for?
 * @raise
 * @return bool true or raise error
 */
function maxercalls_admin_createmaxer($args)
{

    extract($args);

    if (!xarVarFetch('objectid',    'id',     $objectid, $objectid, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('invalid',     'array', $invalid,  array(), XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('personid',    'int:1:', $personid,  $personid,  XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('ric',         'int:1:', $ric,  $ric,  XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('maxernumber', 'int:1:', $maxernumber,  $maxernumber,  XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('function',    'int:1:', $function,  $function,  XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('maxerstatus', 'int:1:', $maxerstatus,  $maxerstatus,  XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('remark',      'str:1:', $remark,  $remark,  XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('program',     'str:1:200', $program,    $program,    XARVAR_NOT_REQUIRED)) return;
    /* Argument check - make sure that all required arguments are present
     * and in the right format, if not then return to the add form with the
     * values that are there and a message with a session var. If you perform
     * this check now, you could do away with the check in the API along with
     * the exception that comes with it.
    TODO: Create the function here. Check for double entries
    $item = xarModAPIFunc('maxercalls',
                          'user',
                          'validateitem',
                          array('name' => $name));
*/
    // Argument check
    $invalid = array();
    if (empty($ric) || !is_numeric($ric)) {
        $invalid['ric'] = 1;
        $ric = '';
    }
    if (empty($maxerstatus) || !is_numeric($maxerstatus)) {
        $invalid['maxerstatus'] = 1;
        $maxerstatus = '';
    }
/*
    if (!empty($name) && $item['name'] == $name) {
        $invalid['duplicate'] = 1;
    }
*/
    // check if we have any errors
    if (count($invalid) > 0) {

        return xarModFunc('maxercalls', 'admin', 'newmaxer',
                          array('personid'    => $personid,
                                'ric'         => $ric,
                                'maxernumber' => $maxernumber,
                                'function'    => $function,
                                'program'     => $program,
                                'maxerstatus' => $maxerstatus,
                                'remark'      => $remark,
                                'invalid' => $invalid));
    }
    /* Confirm authorisation code. */
    if (!xarSecConfirmAuthKey()) return;
    /* The API function is called. Note that the name of the API function and
     * the name of this function are identical, this helps a lot when
     * programming more complex modules. The arguments to the function are
     * passed in as their own arguments array
     */
    $maxerid = xarModAPIFunc('maxercalls',
                             'admin',
                             'createmaxer',
                             array(
                                  'personid'    => $personid,
                                  'ric'         => $ric,
                                  'maxernumber' => $maxernumber,
                                  'function'    => $function,
                                  'program'     => $program,
                                  'maxerstatus' => $maxerstatus,
                                  'remark'      => $remark));

    if (!isset($maxerid) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; // throw back
    /* This function generated no output, and so now it is complete we redirect
     * the user to an appropriate page for them to carry on their work
     */
    xarResponseRedirect(xarModURL('maxercalls', 'admin', 'viewmaxers'));
    /* Return true, in this case */
    return true;
}
?>

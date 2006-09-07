<?php
/**
 * Create a new ITSP
 *
 * @package modules
 * @copyright (C) 2005-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage ITSP Module
 * @link http://xaraya.com/index.php/release/572.html
 * @author ITSP Module Development Team
 */
/**
 * Create a new ITSP
 *
 * Standard function to create a new ITSP
 * This is a standard function that is called with the results of the
 * form supplied by xarModFunc('itsp','user','new') to create a new item
 *
 * @author ITSP module development team
 * @param  $ 'name' the name of the item to be created
 * @param  $ 'number' the number of the item to be created
 */
function itsp_user_create($args)
{
    extract($args);

    if (!xarVarFetch('objectid',      'id',     $objectid,      $objectid, XARVAR_NOT_REQUIRED)) return; //??
    if (!xarVarFetch('invalid',       'array',  $invalid,       array(), XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('userid',        'int:1:', $userid,        xarUserGetVar('uid'),  XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('planid',        'int:1:', $planid,        $planid,  XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('itspstatus',    'int:1:', $itspstatus,    0,    XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('datesubm',      'int:1:', $datesubm,      $datesubm,  XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('dateappr',      'int:1:', $dateappr,      $dateappr,  XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('datecertreq',   'int:1:', $datecertreq,   $datecertreq,  XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('datecertaward', 'int:1:', $datecertaward, $datecertaward,  XARVAR_NOT_REQUIRED)) return;

    /* Argument check - make sure that all required arguments are present
     * and in the right format, if not then return to the add form with the
     * values that are there and a message with a session var. If you perform
     * this check now, you could do away with the check in the API along with
     * the exception that comes with it.
    // Check to see if user can submit an ITSP?
    $item = xarModAPIFunc('itsp',
                          'user',
                          'validateitem',
                          array('name' => $name));
     */
    // Argument check
    $invalid = array();
    if (empty($planid) || !is_numeric($planid)) {
        $invalid['planid'] = 1;
        $number = '';
    }
    // check if we have any errors
    if (count($invalid) > 0) {
        /* If we get here, we have encountered errors.
         * Send the user back to the user_new form
         * call the user_new function and return the template vars
         */
        return xarModFunc('itsp', 'user', 'new',
                          array('userid'        => $userid,
                                'planid'        => $planid,
                                'itspstatus'    => $itspstatus,
                                'datesubm'      => $datesubm,
                                'dateappr'      => $dateappr,
                                'datecertreq'   => $datecertreq,
                                'datecertaward' => $datecertaward,
                                'invalid'       => $invalid));
    }
    // Confirm authorisation code.
    if (!xarSecConfirmAuthKey()) return;
    // Create the ITSP
    $itspid = xarModAPIFunc('itsp',
                            'user',
                            'create',
                              array('userid'        => $userid,
                                    'planid'        => $planid,
                                    'itspstatus'    => $itspstatus,
                                    'datesubm'      => 0,
                                    'dateappr'      => 0,
                                    'datecertreq'   => 0,
                                    'datecertaward' => 0));
    // The return value of the function is checked here

    if (!isset($itspid) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; // throw back
    xarSessionSetVar('statusmsg', xarML('ITSP was successfully created!'));
    /* This function generated no output, and so now it is complete we redirect
     * the user to an appropriate page for them to carry on their work
     */
    xarResponseRedirect(xarModURL('itsp', 'user', 'itsp', array('itspid'=> $itspid)));
    /* Return true, in this case */
    return true;
}
?>

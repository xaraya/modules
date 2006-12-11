<?php
/**
 * Create a new plan
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
 * Create a new plan
 *
 * Standard function to create a new plan
 * This is a standard function that is called with the results of the
 * form supplied by xarModFunc('itsp','admin','new') to create a new item
 *
 * @author michelv <michelv@xarayahosting.nl>
 * @param  int credits
 * @param  int mincredit
 * @param string dateopen
 */
function itsp_admin_create($args)
{
    extract($args);

    // Get parameters from whatever input we need.
    if (!xarVarFetch('planid',     'id',     $planid,     $planid,    XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('objectid',   'id',     $objectid,   $objectid,  XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('planname',   'str:1:', $planname,   '',  XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('plandesc',   'str:1:', $plandesc,   '',  XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('planrules',  'str:1:', $planrules,  '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('credits',    'int:1:', $credits,    '',   XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('mincredit',  'int:1:', $mincredit,  '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('dateopen',   'str',    $dateopen,   '',  XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('dateclose',  'str',    $dateclose,  '', XARVAR_NOT_REQUIRED)) return;

    if (!xarVarFetch('invalid', 'array',  $invalid, $invalid, XARVAR_NOT_REQUIRED)) return;

    /* Argument check

    $item = xarModAPIFunc('itsp',
                          'user',
                          'validateitem',
                          array('name' => $name));
     */
    // Argument check
    $invalid = array();
    if (empty($credits) || !is_numeric($credits)) {
        $invalid['credits'] = 1;
        $number = '';
    }
    if (empty($planname) || !is_string($planname)) {
        $invalid['plan'] = 1;
        $name = '';
    }
/*
    if (!empty($name) && $item['name'] == $name) {
        $invalid['duplicate'] = 1;
    }
*/
    // check if we have any errors
    if (count($invalid) > 0) {
        return xarModFunc('itsp', 'admin', 'new',
                          array('invalid' => $invalid,
                                'planname' => $planname,
                                'plandesc' => $plandesc,
                                'planrules' => $planrules,
                                'credits' => $credits,
                                'mincredit' => $mincredit,
                                'dateopen' => $dateopen,
                                'dateclose' => $dateclose));
    }

     /* Confirm authorisation code. */
    if (!xarSecConfirmAuthKey()) return;
    /* The API function is called. */
    $planid = xarModAPIFunc('itsp',
                          'admin',
                          'create',
                          array('planname' => $planname,
                                'plandesc' => $plandesc,
                                'planrules' => $planrules,
                                'credits' => $credits,
                                'mincredit' => $mincredit,
                                'dateopen'  => $dateopen,
                                'dateclose' => $dateclose
                                ));
    /* The return value of the function is checked here, and if the function
     * suceeded then an appropriate message is posted.
     */
    if (!isset($planid) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; // throw back
    xarSessionSetVar('statusmsg', xarML('ITSP Plan was successfully created!'));
    /* This function generated no output, and so now it is complete we redirect
     * the user to an appropriate page for them to carry on their work
     */
    xarResponseRedirect(xarModURL('itsp', 'admin', 'view'));
    /* Return true, in this case */
    return true;
}
?>

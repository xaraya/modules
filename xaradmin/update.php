<?php
/**
 * Standard function to update a current item
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
 * Standard function to update a plan
 *
 * This function is called with the results of the
 * form supplied by xarModFunc('itsp','admin','modify') to update a current plan
 *
 * @author ITSP module development team
 * @param  $ 'planid' the id of the item to be updated
 * @param  $ 'planname' the name of the item to be updated
 * @param  $ 'plandesc' the description of the item to be updated
 */
function itsp_admin_update($args)
{
    extract($args);

    if (!xarVarFetch('planid',     'id',     $planid,     $planid,    XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('objectid',   'id',     $objectid,   $objectid,  XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('invalid',    'array',  $invalid,    $invalid,   XARVAR_NOT_REQUIRED)) return;

    if (!xarVarFetch('planname',   'str:1:', $planname,   $planname,  XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('plandesc',   'str:1:', $plandesc,   $plandesc,  XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('planrules',  'str:1:', $planrules,  $planrules, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('credits',    'int:1:', $credits,    $credits,   XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('mincredit',  'int:1:', $mincredit,  $mincredit, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('dateopen',   'int:1:', $dateopen,   $dateopen,  XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('dateclose',  'int:1:', $dateclose,  $dateclose, XARVAR_NOT_REQUIRED)) return;

    if (!empty($objectid)) {
        $planid = $objectid;
    }

    /* Confirm authorisation code.
     */
    if (!xarSecConfirmAuthKey()) return;
    /* Notable by its absence there is no security check here.  This is because
     * the security check is carried out within the API function and as such we
     * do not duplicate the work here
     */

    $invalid = array();
    if (empty($credits) || !is_numeric($credits)) {
        $invalid['credits'] = 1;
        $number = '';
    }
    if (empty($planname) || !is_string($planname)) {
        $invalid['planname'] = 1;
        $name = '';
    }

    /* check if we have any errors */
    if (count($invalid) > 0) {
        /* call the admin_new function and return the template vars
         * (you need to copy admin-new.xd to admin-create.xd here)
         */
        return xarModFunc('itsp', 'admin', 'modify',
                          array('invalid'   => $invalid,
                                'planid'    => $planid,
                                'planname'  => $planname,
                                'plandesc'  => $plandesc,
                                'planrules' => $planrules,
                                'credits'   => $credits,
                                'mincredit' => $mincredit,
                                'dateopen'  => $dateopen,
                                'dateclose' => $dateclose));
    }

    /* The API function is called: update plan
     */
    if (!xarModAPIFunc('itsp',
                       'admin',
                       'update',
                       array('planid'    => $planid,
                             'planname'  => $planname,
                             'plandesc'  => $plandesc,
                             'planrules' => $planrules,
                             'credits'   => $credits,
                             'mincredit' => $mincredit,
                             'dateopen'  => strtotime($dateopen),
                             'dateclose' => strtotime($dateclose)
                             )
                       )) {
        return; /* throw back */
    }
    xarSessionSetVar('statusmsg', xarML('ITSP Plan was successfully updated!'));
    /* This function generated no output, and so now it is complete we redirect
     * the user to an appropriate page for them to carry on their work
     */
    xarResponseRedirect(xarModURL('itsp', 'admin', 'view'));
    /* Return */
    return true;
}
?>
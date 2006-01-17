<?php
/**
 * Standard function to update a current item
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage ITSP Module
 * @link http://xaraya.com/index.php/release/572.html
 * @author ITSP Module Development Team
 */
/**
 * Standard function to update a current item
 *
 * This function is called with the results of the
 * form supplied by xarModFunc('itsp','admin','modify') to update a current item
 *
 * @author ITSP module development team
 * @param  $ 'pitemid' the id of the item to be updated
 * @param  $ 'pitemname' the name of the item to be updated
 * @param  $ 'pitemdesc' the description of the item to be updated
 */
function itsp_admin_update_pitem($args)
{
    extract($args);

    if (!xarVarFetch('pitemid',    'id',     $pitemid,     $pitemid,    XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('objectid',   'id',     $objectid,   $objectid,  XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('invalid',    'array',  $invalid,    $invalid,   XARVAR_NOT_REQUIRED)) return;

    if (!xarVarFetch('pitemname',  'str:1:', $pitemname,   $pitemname,  XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('pitemdesc',  'str:1:', $pitemdesc,   $pitemdesc,  XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('pitemrules', 'str:1:', $pitemrules,  $pitemrules, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('credits',    'int:1:', $credits,    $credits,   XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('mincredit',  'int:1:', $mincredit,  $mincredit, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('dateopen',   'isset',  $dateopen,   $dateopen,  XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('dateclose',  'isset',  $dateclose,  $dateclose, XARVAR_NOT_REQUIRED)) return;

    /* At this stage we check to see if we have been passed $objectid, the
     * generic item identifier.  This could have been passed in by a hook or
     * through some other function calling this as part of a larger module, but
     * if it exists it overrides $exid
     *
     * Note that this module couuld just use $objectid everywhere to avoid all
     * of this munging of variables, but then the resultant code is less
     * descriptive, especially where multiple objects are being used.  The
     * decision of which of these ways to go is up to the module developer
     */
    if (!empty($objectid)) {
        $pitemid = $objectid;
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
    if (empty($pitemname) || !is_string($pitemname)) {
        $invalid['pitemname'] = 1;
        $name = '';
    }

    /* check if we have any errors */
    if (count($invalid) > 0) {
        /* call the admin_new function and return the template vars
         * (you need to copy admin-new.xd to admin-create.xd here)
         */
        return xarModFunc('itsp', 'admin', 'modify_pitem',
                          array('invalid'   => $invalid,
                                'pitemid'    => $pitemid,
                                'pitemname'  => $pitemname,
                                'pitemdesc'  => $pitemdesc,
                                'pitemrules' => $pitemrules,
                                'credits'   => $credits,
                                'mincredit' => $mincredit,
                                'dateopen'  => $dateopen,
                                'dateclose' => $dateclose));
    }

    /* The API function is called: update item.
     */
    if ((!empty($dateopen)) && !is_numeric($dateopen)) {
         $dateopen = strtotime($dateopen);
    }
    if ((!empty($dateclose)) && !is_numeric($dateclose)) {
         $dateopen = strtotime($dateclose);
    }
    if (!xarModAPIFunc('itsp',
                       'admin',
                       'update_pitem',
                       array('pitemid'    => $pitemid,
                             'pitemname'  => $pitemname,
                             'pitemdesc'  => $pitemdesc,
                             'pitemrules' => $pitemrules,
                             'credits'   => $credits,
                             'mincredit' => $mincredit,
                             'dateopen'  => $dateopen,
                             'dateclose' => $dateclose
                             )
                       )) {
        return; /* throw back */
    }
    xarSessionSetVar('statusmsg', xarML('Plan item was successfully updated!'));
    /* This function generated no output, and so now it is complete we redirect
     * the user to an appropriate page for them to carry on their work
     */
    xarResponseRedirect(xarModURL('itsp', 'admin', 'view_pitems'));
    /* Return */
    return true;
}
?>
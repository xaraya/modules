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
 * Standard function to update a current item
 *
 * This function is called with the results of the
 * form supplied by xarModFunc('itsp','admin','modify') to update a current item
 *
 * @author ITSP module development team
 * @param  int  pitemid the id of the item to be updated
 * @param  string pitemname the name of the item to be updated
 * @param  string pitemdesc the description of the item to be updated
 * @param string rule_source. This source of the courses a user can add
                 Possible values: courses (xar Module), internal (self defined), external, open
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
    if (!xarVarFetch('credits',    'int:0:', $credits,    $credits,   XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('mincredit',  'int:0:', $mincredit,  $mincredit, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('dateopen',   'isset',  $dateopen,   $dateopen,  XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('dateclose',  'isset',  $dateclose,  $dateclose, XARVAR_NOT_REQUIRED)) return;

    if (!xarVarFetch('rule_cat',   'str::', $rule_cat,    $rule_cat,   XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('rule_type',  'int::', $rule_type,    $rule_type,   XARVAR_NOT_REQUIRED)) return; // The coursetype
    if (!xarVarFetch('rule_source','str::', $rule_source,    $rule_source,   XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('rule_level', 'int::', $rule_level,   $rule_level,   XARVAR_NOT_REQUIRED)) return;

    if (!empty($objectid)) {
        $pitemid = $objectid;
    }

    /* Confirm authorisation code.
     */
    if (!xarSecConfirmAuthKey()) return;
    // Sanity check.
    //TODO: MichelV: Why do credits and mincredit not validate to integers?
    $invalid = array();
    if (empty($pitemname) || !is_string($pitemname)) {
        $invalid['pitemname'] = 1;
    }

    /* check if we have any errors */
    if (count($invalid) > 0) {
        /* call the admin_new function and return the template vars
         * (you need to copy admin-new.xd to admin-create.xd here)
         */
        return xarModFunc('itsp', 'admin', 'modify_pitem',
                          array('pitemid'     => $pitemid,
                                'invalid'     => $invalid,
                                'pitemname'   => $pitemname,
                                'pitemdesc'   => $pitemdesc,
                                'pitemrules'  => $pitemrules,
                                'credits'     => $credits,
                                'mincredit'   => $mincredit,
                                'dateopen'    => $dateopen,
                                'dateclose'   => $dateclose,
                                'rule_cat'    => $rule_cat,
                                'rule_type'   => $rule_type,
                                'rule_level'  => $rule_level,
                                'rule_source' => $rule_source));
    }

    /* The API function is called: update item.
     */
    if ((!empty($dateopen)) && !is_numeric($dateopen)) {
         $dateopen = strtotime($dateopen);
    }
    if ((!empty($dateclose)) && !is_numeric($dateclose)) {
         $dateopen = strtotime($dateclose);
    }
    // Format the rule
    $pitemrules = "coursetype:$rule_type;level:$rule_level;category:$rule_cat;source:$rule_source;";

    if (!xarModAPIFunc('itsp',
                       'admin',
                       'update_pitem',
                       array('pitemid'    => $pitemid,
                            'pitemname'   => $pitemname,
                            'pitemdesc'   => $pitemdesc,
                            'pitemrules'  => $pitemrules,
                            'credits'     => $credits,
                            'mincredit'   => $mincredit,
                            'dateopen'    => $dateopen,
                            'dateclose'   => $dateclose
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
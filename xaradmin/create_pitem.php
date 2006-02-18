<?php
/**
 * Create a new plan item
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
 * Create a new plan item
 *
 * Standard function to create a new pitem
 * This is a standard function that is called with the results of the
 * form supplied by xarModFunc('itsp','admin','new_pitem') to create a new plan item
 *
 * @author ITSP module development team
 * @param  string 'pitemname' the name of the item to be created
 * @param  string 'pitemdesc' the description of the item to be created
 * @param int 'mincredit'
 * @param int 'credits'
 * @param int 'rule_cat'
 * @param int 'rule_level'
 * @param int 'rule_type'
 * @return mixed true on succes, with redirect URL
 */
function itsp_admin_create_pitem($args)
{
    extract($args);

    // Get parameters from whatever input we need.
    if (!xarVarFetch('pitemid',    'id',     $pitemid,    $pitemid,    XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('objectid',   'id',     $objectid,   $objectid,  XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('pitemname',  'str:1:', $pitemname,  $pitemname,  XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('pitemdesc',  'str:1:', $pitemdesc,  $pitemdesc,  XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('pitemrules', 'str:1:', $pitemrules, $pitemrules, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('credits',    'int:1:', $credits,    $credits,   XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('mincredit',  'int:1:', $mincredit,  $mincredit, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('dateopen',   'int:1:', $dateopen,   $dateopen,  XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('dateclose',  'int:1:', $dateclose,  $dateclose, XARVAR_NOT_REQUIRED)) return;

    if (!xarVarFetch('rule_cat',   'int:1:', $rule_cat,    $rule_cat,   XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('rule_type',  'int::', $rule_type,    $rule_type,   XARVAR_NOT_REQUIRED)) return; // The coursetype
    if (!xarVarFetch('rule_source','enum:internal:external:open:all', $rule_source,    $rule_source,   XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('rule_level', 'int::', $rule_level,   $rule_level,   XARVAR_NOT_REQUIRED)) return;

    if (!xarVarFetch('invalid', 'array',  $invalid, $invalid, XARVAR_NOT_REQUIRED)) return;

    /* Argument check

    $item = xarModAPIFunc('itsp',
                          'user',
                          'validateitem',
                          array('name' => $name));
     */
    // Argument check
    $invalid = array();
    if (empty($mincredit) || !is_numeric($mincredit)) {
        $invalid['mincredit'] = 1;
        $number = '';
    }
    if (empty($pitemname) || !is_string($pitemname)) {
        $invalid['pitemname'] = 1;
        $name = '';
    }
/*
    if (!empty($name) && $item['name'] == $name) {
        $invalid['duplicate'] = 1;
    }
*/
    // check if we have any errors
    if (count($invalid) > 0) {
        return xarModFunc('itsp', 'admin', 'new_pitem',
                          array('invalid'     => $invalid,
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
                                'rule_course' => $rule_source));
    }

     /* Confirm authorisation code. */
    if (!xarSecConfirmAuthKey()) return;

    // Format the rule
    $pitemrules = "type:$rule_type;level:$rule_level;category:$rule_cat;source:$rule_source";

    if(empty($credits)) {
        $credits = '';
    }

    /* The API function is called. */
    $pitemid = xarModAPIFunc('itsp',
                          'admin',
                          'create_pitem',
                          array('pitemname'  => $pitemname,
                                'pitemdesc'  => $pitemdesc,
                                'pitemrules' => $pitemrules,
                                'credits'    => $credits,
                                'mincredit'  => $mincredit,
                                'dateopen'   => $dateopen,
                                'dateclose'  => $dateclose));
    /* The return value of the function is checked here, and if the function
     * suceeded then an appropriate message is posted.
     */
    if (!isset($pitemid) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; // throw back
    xarSessionSetVar('statusmsg', xarML('ITSP Plan item was successfully created!'));
    /* This function generated no output, and so now it is complete we redirect
     * the user to an appropriate page for them to carry on their work
     */
    xarResponseRedirect(xarModURL('itsp', 'admin', 'view_pitems'));
    /* Return true, in this case */
    return true;
}
?>

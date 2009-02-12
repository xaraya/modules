<?php
/**
 * LabAffiliate Module - initialization functions
 *
 * @package modules
 * @copyright (C) 2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage LabAffiliate Module
 * @link http://xaraya.com/index.php/release/919
 * @author LabAffiliate Module Development Team
 */
function labaffiliate_affiliate_create($args)
{
    extract($args);

    if (!xarVarFetch('programid',  'id', $programid,  $programid,  XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('uplineid',  'id', $uplineid,  $uplineid,  XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('userid',    'id', $userid,    $userid,    XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('primaryprogramid',    'id', $primaryprogramid,    $primaryprogramid,    XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('secondaryprogramid',    'int:1:', $secondaryprogramid,    $secondaryprogramid,    XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('marketing_copy', 'str:1',  $marketing_copy, $marketing_copy, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('invalid', 'array',  $invalid, $invalid, XARVAR_NOT_REQUIRED)) return;

    // Argument check
    $invalid = array();

    if (empty($uplineid) || !is_numeric($uplineid)) {
        $invalid[] = 'Upline ID';
    }

    if (empty($userid) || !is_numeric($userid)) {
        $invalid[] = 'User ID';
    }

    if (empty($primaryprogramid) || !is_numeric($primaryprogramid)) {
        $invalid[] = 'Primary Program ID';
    }

    if (empty($secondaryprogramid) || !is_numeric($secondaryprogramid)) {
        $invalid[] = 'Secondary Program ID';
    }

    if (empty($marketing_copy) || !is_string($marketing_copy)) {
        $marketing_copy = '';
    }
//die(var_dump($invalid));
    // check if we have any errors
    if (count($invalid) > 0) {
        /* If we get here, we have encountered errors.
         * Send the user back to the admin_new form
         * call the admin_new function and return the template vars
         */
        return xarModFunc('labaffiliate', 'affiliate', 'new',
                          array('programid' => $programid,
							'invalid' => $invalid));
    }

    if (!xarSecConfirmAuthKey()) return;

    $affiliateid = xarModAPIFunc('labaffiliate',
                          'affiliate',
                          'create',
                          array('programid' => $programid,
							'uplineid' => $uplineid,
							'userid' => $userid,
							'primaryprogramid' => $primaryprogramid,
							'secondaryprogramid' => $secondaryprogramid,
							'marketing_copy' => $marketing_copy));

    if (!isset($affiliateid) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; // throw back

    xarResponseRedirect(xarModURL('labaffiliate', 'admin', 'display', array('programid' => $programid)));
    
    /* Return true, in this case */
    return true;
}

?>
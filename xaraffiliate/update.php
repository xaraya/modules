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
function labaffiliate_affiliate_update($args)
{
    extract($args);

    if (!xarVarFetch('affiliateid',  'id', $affiliateid,  $affiliateid,  XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('uplineid',  'id', $uplineid,  $uplineid,  XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('userid',    'int:1:', $userid,    $userid,    XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('primaryprogramid', 'str:1',  $primaryprogramid, $primaryprogramid, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('secondaryprogramid', 'str:1',  $secondaryprogramid, $secondaryprogramid, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('marketing_copy', 'str:1',  $marketing_copy, $marketing_copy, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('invalid', 'array',  $invalid, $invalid, XARVAR_NOT_REQUIRED)) return;

    if (!empty($objectid)) {
        $affiliateid = $objectid;
    }

    if (!xarSecConfirmAuthKey()) return;

    // Argument check
    $invalid = array();

    if (empty($affiliateid) || !is_numeric($affiliateid)) {
        $invalid[] = 'Affiliate ID';
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

    // check if we have any errors
    if (count($invalid) > 0) {
        echo "<pre>"; print_r($invalid); die("</pre>");
        /* If we get here, we have encountered errors.
         * Send the user back to the admin_new form
         * call the admin_new function and return the template vars
         */
        return xarModFunc('labaffiliate', 'affiliate', 'modify',
                          array(
							'affiliateid' => $affiliateid,
							'primaryprogramid' => $primaryprogramid,
							'secondaryprogramid' => $secondaryprogramid,
							'marketing_copy' => $marketing_copy,
							'invalid' => $invalid));
    }

    $item = xarModAPIFunc('labaffiliate',
                          'affiliate',
                          'get',
                          array('affiliateid' => $affiliateid));

    $affiliateid = xarModAPIFunc('labaffiliate',
                          'affiliate',
                          'update',
                          array('affiliateid' => $affiliateid,
							'primaryprogramid' => $primaryprogramid,
							'secondaryprogramid' => $secondaryprogramid,
							'marketing_copy' => $marketing_copy,
							'invalid' => $invalid));

    if (!isset($affiliateid) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; // throw back

    xarResponseRedirect(xarModURL('labaffiliate', 'affiliate', 'display', array('affiliateid' => $affiliateid)));

    /* Return */
    return true;
}

?>
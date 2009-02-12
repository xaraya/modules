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
function labaffiliate_membership_update($args)
{
    extract($args);

    if (!xarVarFetch('affiliateid', 'id', $affiliateid, $affiliateid, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('programid', 'id', $programid, $programid, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('program_key', 'str:1:', $program_key, $program_key, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('invalid', 'array', $invalid, $invalid, XARVAR_NOT_REQUIRED)) return;

    if (!empty($objectid)) {
        $membershipid = $objectid;
    }

    if (!xarSecConfirmAuthKey()) return;

    // Argument check
    $invalid = array();
    if (empty($programid) || !is_numeric($programid)) {
        $invalid[] = 'Programid ID';
    }
    if (empty($affiliateid) || !is_numeric($affiliateid)) {
        $invalid[] = 'affiliate ID';
    }
    if (empty($program_key) || !is_string($program_key)) {
        $invalid[] = 'Program Key';
    }

    // check if we have any errors
    if (count($invalid) > 0) {
        /* If we get here, we have encountered errors.
         * Send the user back to the admin_new form
         * call the admin_new function and return the template vars
         */
        return xarModFunc('labaffiliate', 'membership', 'modify',
                          array('programid' => $programid,
                          	'affiliateid' => $affiliateid,
							'program_key' => $program_key,
							'invalid' => $invalid));
    }

    $item = xarModAPIFunc('labaffiliate',
                          'membership',
                          'get',
                          array('programid' => $programid,
                          		'affiliateid' => $affiliateid));

    $membershipid = xarModAPIFunc('labaffiliate',
                          'membership',
                          'update',
                          array('programid' => $programid,
                          	'affiliateid' => $affiliateid,
							'program_key' => $program_key,
							'invalid' => $invalid));

    if (!isset($membershipid) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; // throw back

    xarResponseRedirect(xarModURL('labaffiliate', 'affiliate', 'display', array('affiliateid' => $affiliateid)));

    /* Return */
    return true;
}

?>
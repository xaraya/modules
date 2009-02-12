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
function labaffiliate_membershipapi_update($args)
{
    extract($args);

    // Argument check
    $invalid = array();
    if (empty($membershipid) || !is_numeric($membershipid)) {
        $invalid[] = 'membership ID';
    }
    if (empty($affiliateid) || !is_numeric($affiliateid)) {
        $invalid[] = 'Affiliate ID';
    }
    if (empty($program_key) || !is_string($program_key)) {
        $invalid[] = 'Program Key';
    }
    
    if (empty($marketing_copy) || !is_string($marketing_copy)) {
        $marketing_copy = '';
    }

    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
            join(', ', $invalid), 'membership', 'update', 'labAffiliate');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
            new SystemException($msg));
        return;
    }

	$item = xarModAPIFunc('labaffiliate',
		'membership',
		'get',
		array('membershipid' => $membershipid));
	/*Check for exceptions */
	if (!isset($item) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; // throw back

    if (!xarSecurityCheck('EditProgramMembership', 1, 'Membership', "All:All:$membershipid")) {
        return;
    }

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

	$labaffiliate_membership_table = $xartable['labaffiliate_membership'];

	$query = "UPDATE $labaffiliate_membership_table
				SET xar_programid = ?,
					xar_affiliateid = ?,
					xar_program_key = ?,
                    xar_marketing_copy = ?
				WHERE
					xar_membershipid = ?";

    $bindvars = array((int) $programid, (int) $affiliateid, (string) $program_key, $marketing_copy, (int) $membershipid);

	$result = &$dbconn->Execute($query,$bindvars);

	if (!$result) return;

    $item['module'] = 'labaffiliate';
    $item['itemid'] = $membershipid;
    $item['itemtype'] = 3;
    xarModCallHooks('item', 'update', $membershipid, $item);

	return true;

}

?>
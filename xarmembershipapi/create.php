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
function labaffiliate_membershipapi_create($args)
{
    extract($args);

    // Argument check
    $invalid = array();

    if (empty($programid) || !is_numeric($programid)) {
        $invalid[] = 'Program ID';
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
            join(', ', $invalid), 'membership', 'create', 'labaffiliate');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
            new SystemException($msg));
        return;
    }

    if (!xarSecurityCheck('AddProgramMembership', 1, 'Membership', "All:All:All")) {
        return;
    }

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

	$labaffiliate_membership_table = $xartable['labaffiliate_membership'];

    $nextId = $dbconn->GenId($labaffiliate_membership_table);


    $query = "INSERT INTO $labaffiliate_membership_table (
					xar_membershipid,
					xar_programid,
					xar_affiliateid,
					xar_program_key,
                    xar_marketing_copy
					)
	            VALUES (?,?,?,?,?)";

    $bindvars = array($nextId, (int) $programid, (int) $affiliateid, (string) $program_key, $marketing_copy);

    $result = &$dbconn->Execute($query,$bindvars);

    if (!$result) return;

    $membershipid = $dbconn->PO_Insert_ID($labaffiliate_membership_table, 'xar_affiliateid');

    $item = $args;
    $item['module'] = 'labaffiliate';
    $item['itemid'] = $membershipid;
    $item['itemtype'] = 3;
    xarModCallHooks('item', 'create', $membershipid, $item);

    return $membershipid;
}

?>

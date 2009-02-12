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
function labaffiliate_membershipapi_get($args)
{
    extract($args);

    if (!isset($membershipid) || !is_numeric($membershipid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
            'membership ID', 'membership', 'get', 'labaffiliate');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
            new SystemException($msg));
        return;
    }

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

	$labaffiliate_membership_table = $xartable['labaffiliate_membership'];

    $query = "SELECT xar_membershipid,
    				xar_programid,
					xar_affiliateid,
					xar_program_key
              FROM $labaffiliate_membership_table
              WHERE xar_membershipid = ?";
    $result = &$dbconn->Execute($query,array($membershipid));

    if (!$result) return;

    if ($result->EOF) {
        $result->Close();
        $msg = xarML('This membership does not exist');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'ID_NOT_EXIST',
            new SystemException(__FILE__ . '(' . __LINE__ . '): ' . $msg));
        return;
    }

    list($membershipid, $programid, $affiliateid, $program_key) = $result->fields;

    $result->Close();

    if (!xarSecurityCheck('ReadProgramMembership', 1, 'Membership', "All:All:$membershipid")) {
        return;
    }


	$item = array('membershipid' => $membershipid,
		'programid' => $programid,
		'affiliateid' => $affiliateid,
		'program_key' => $program_key);

    return $item;
}

?>
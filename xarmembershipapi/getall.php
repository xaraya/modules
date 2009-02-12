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
function labaffiliate_membershipapi_getall($args)
{
    extract($args);

	$invalid = array();

    if ((!isset($affiliateid) || !is_numeric($affiliateid))
        &&
        (!isset($programid) || !is_numeric($programid))) {
		$invalid[] = 'Affiliate ID or Program ID';
	}
    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
            join(', ', $invalid), 'membership', 'getall', 'labAffiliate');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
            new SystemException($msg));
        return;
    }

    $items = array();

    if (!xarSecurityCheck('ViewProgramMembership')) return;

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

	$labaffiliate_membership_table = $xartable['labaffiliate_membership'];

	$sql = "SELECT
				xar_programid,
				xar_affiliateid,
				xar_program_key
			FROM $labaffiliate_membership_table";
    $bindvars = array();
    if(!empty($affiliateid)) {
	    $sql .= " WHERE xar_affiliateid = ?";
        $bindvars[] = $affiliateid;
    } else {
        $sql .= " WHERE xar_programid = ?";
        $bindvars[] = $programid;
    }
            
	$result = $dbconn->execute($sql, $bindvars);

	if (!$result) return;

    for (; !$result->EOF; $result->MoveNext()) {
        list($programid, $affiliateid, $program_key) = $result->fields;
        if (xarSecurityCheck('ViewProgramMembership', 0, 'Membership', "All:All:$affiliateid")) {
            $items[] = array('programid' => $programid,
                            'affiliateid' => $affiliateid,
                            'program_key' => $program_key);
        }
    }

    $result->Close();

    return $items;
}

?>

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
function labaffiliate_adminapi_update($args)
{
	extract($args);

	// Argument check
	$invalid = array();
	if (empty($programid) || !is_numeric($programid)) {
		$invalid['programid'] = 1;
		$programid = 0;
	}
    if (empty($program_name) || !is_string($program_name)) {
        $invalid[] = 'Program Name';
    }

    if (empty($programurl) || !is_string($programurl)) {
//        $invalid[] = 'Program URL';
    }

    if (empty($affiliateurl) || !is_string($affiliateurl)) {
//        $invalid[] = 'Affiliate URL';
    }

    if (empty($details) || !is_string($details)) {
//        $invalid[] = 'Details';
    }

    if (empty($marketing_copy) || !is_string($marketing_copy)) {
//        $invalid[] = 'Marketing Copy';
    }

	if (count($invalid) > 0) {
		$msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
			join(', ', $invalid), 'admin', 'update', 'labAffiliate');
		xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
			new SystemException($msg));
		return;
	}

	$item = xarModAPIFunc('labaffiliate', 'user', 'get', array('programid' => $programid));
	/*Check for exceptions */
	if (!isset($item) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; // throw back

	if (!xarSecurityCheck('EditProgram', 1, 'Program', "$item[program_name]:All:$programid")) {
		return;
	}

	$dbconn =& xarDBGetConn();
	$xartable =& xarDBGetTables();

	$labaffiliate_programs_table = $xartable['labaffiliate_programs'];

	$query = "UPDATE $labaffiliate_programs_table
				SET
					xar_programid =?,
					xar_program_name =?,
					xar_programurl =?,
					xar_affiliateurl =?,
					xar_details =?,
					xar_marketing_copy =?,
                    xar_status = ?
				WHERE
					xar_programid = ?";

    $bindvars = array((int) $programid, (string) $program_name, (string) $programurl, (string) $affiliateurl, (string) $details, (string) $marketing_copy, (string) $status, (int) $programid);

	$result = &$dbconn->Execute($query,$bindvars);

	if (!$result) return;

    $item['module'] = 'labaffiliate';
    $item['itemid'] = $programid;
    $item['itemtype'] = 1;
    xarModCallHooks('item', 'update', $programid, $item);

	return true;
}

?>
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
function labaffiliate_userapi_get($args)
{
    extract($args);

    if (!isset($programid) || !is_numeric($programid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
            'program ID', 'user', 'get', 'labAffiliate');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
            new SystemException($msg));
        return;
    }

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

	$labaffiliate_programs_table = $xartable['labaffiliate_programs'];

    $query = "SELECT xar_programid,
					xar_program_name,
					xar_programurl,
					xar_affiliateurl,
					xar_details,
					xar_marketing_copy,
                    xar_status
              FROM $labaffiliate_programs_table
              WHERE xar_programid = ?";
    $result = &$dbconn->Execute($query,array($programid));

    if (!$result) return;

    if ($result->EOF) {
        $result->Close();
        $msg = xarML('This program does not exist');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'ID_NOT_EXIST',
            new SystemException(__FILE__ . '(' . __LINE__ . '): ' . $msg));
        return;
    }

    list($programid,$program_name,$programurl,$affiliateurl,$details,$marketing_copy,$status) = $result->fields;

    $result->Close();

    if (!xarSecurityCheck('ReadProgram', 1, 'Program', "$program_name:All:$programid")) {
        return;
    }


	$item = array('programid' => $programid,
				'program_name' => $program_name,
				'programurl' => $programurl,
				'affiliateurl' => $affiliateurl,
				'details' => $details,
				'marketing_copy' => $marketing_copy,
				'status' => $status);

    return $item;
}

?>
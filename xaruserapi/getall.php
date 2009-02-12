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
function labaffiliate_userapi_getall($args)
{
    extract($args);

    if (!isset($startnum)) {
        $startnum = 1;
    }
    if (!isset($numitems)) {
        $numitems = -1;
    }

    $invalid = array();
    if (!isset($startnum) || !is_numeric($startnum)) {
        $invalid[] = 'startnum';
    }
    if (!isset($numitems) || !is_numeric($numitems)) {
        $numitems = xarModGetVar('labaffiliate', 'itemsperpage');
    }
    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
            join(', ', $invalid), 'user', 'getall', 'labAffiliate');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
            new SystemException($msg));
        return;
    }

    $items = array();

    if (!xarSecurityCheck('ViewProgram')) return;

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

	$labaffiliate_programs_table = $xartable['labaffiliate_programs'];

    $thistype= 1;

    $sql = "SELECT  xar_programid,
					xar_program_name,
					xar_programurl,
					xar_affiliateurl,
					xar_details,
					xar_marketing_copy,
                    xar_status,
                   CASE 
                        WHEN xar_status = 'Active' THEN 1
                        WHEN xar_status = 'Submitted' THEN 2
                        WHEN xar_status = 'Draft' THEN 4
                        WHEN xar_status = 'Archived' THEN 999
                        ELSE 3
                    END AS xar_status_order
				FROM $labaffiliate_programs_table";
    
    $bindvars = array();
    
    if(isset($status) && !empty($status)) {
        $sql .= " WHERE xar_status = ?";
        $bindvars[] = $status;
    }
    
    $sql .= " ORDER BY xar_status_order, xar_program_name";

    $result = $dbconn->SelectLimit($sql, $numitems, $startnum-1, $bindvars);

    if (!$result) return;

    for (; !$result->EOF; $result->MoveNext()) {
        list($programid,$program_name,$programurl,$affiliateurl,$details,$marketing_copy,$status) = $result->fields;
        if (xarSecurityCheck('ViewProgram', 0, 'Program', "$program_name:All:$programid")) {
            $items[] = array(
							'programid' => $programid,
							'program_name' => $program_name,
							'programurl' => $programurl,
							'affiliateurl' => $affiliateurl,
							'details' => $details,
							'marketing_copy' => $marketing_copy,
							'status' => $status);
        }
    }

    $result->Close();

    return $items;
}

?>
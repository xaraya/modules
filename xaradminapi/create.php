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
function labaffiliate_adminapi_create($args)
{
    extract($args);

    // Argument check
    $invalid = array();
    if (empty($program_name) || !is_string($program_name)) {
        $invalid[] = 'program_name';
        $program_name = '';
    }

    if (empty($programurl) || !is_string($programurl)) {
        $invalid[] = 'programurl';
        $programurl = '';
    }

    if (empty($affiliateurl) || !is_string($affiliateurl)) {
        $invalid[] = 'affiliateurl';
        $affiliateurl = '';
    }

    if (empty($details) || !is_string($details)) {
        $invalid[] = 'details';
        $details = '';
    }

    if (empty($marketing_copy) || !is_string($marketing_copy)) {
        $invalid[] = 'marketing_copy';
        $marketing_copy = '';
    }

    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
            join(', ', $invalid), 'admin', 'create', 'labAffiliate');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
            new SystemException($msg));
        return;
    }

    if (!xarSecurityCheck('AddProgram', 1, 'Item', "$program_name:All:All")) {
        return;
    }

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

	$labaffiliate_programs_table = $xartable['labaffiliate_programs'];

    $nextId = $dbconn->GenId($labaffiliate_programs_table);

    $query = "INSERT INTO $labaffiliate_programs_table (
				xar_programid,
				xar_program_name,
				xar_programurl,
				xar_affiliateurl,
				xar_details,
				xar_marketing_copy,
                xar_status)
            VALUES (?,?,?,?,?,?,?)";

    $bindvars = array($nextId, (string) $program_name, (string) $programurl, (string) $affiliateurl, (string) $details, (string) $marketing_copy, (string) $status);

    $result = &$dbconn->Execute($query,$bindvars);

    if (!$result) return;

    $programid = $dbconn->PO_Insert_ID($labaffiliate_programs_table, 'xar_programid');

    $item = $args;
    $item['module'] = 'labaffiliate';
    $item['itemid'] = $programid;
    $item['itemtype'] = 1;
    xarModCallHooks('item', 'create', $programid, $item);

    return $programid;
}

?>

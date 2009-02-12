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
function labaffiliate_membershipapi_find($args)
{
    extract($args);
    
    $invalid = array();
    if (empty($programid) || !is_numeric($programid)) {
        $invalid[] = 'Program ID';
    }
    if (empty($affiliateid) || !is_numeric($affiliateid)) {
        $invalid[] = 'Affiliate ID';
    }

    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
            'membership ID', 'membership', 'find', 'labaffiliate');
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
					xar_program_key,
                    xar_marketing_copy
              FROM $labaffiliate_membership_table
              WHERE xar_programid = ?
              AND xar_affiliateid = ?";
    $result = &$dbconn->Execute($query,array($programid,$affiliateid));

    if (!$result) return;

    if ($result->EOF) {
        $result->Close();
        $emptyitem = array('membershipid' => 0,
                    		'programid' => $programid,
                    		'affiliateid' => $affiliateid,
                    		'program_key' => "",
                    		'marketing_copy' => "");
        return $emptyitem;
/*
        $msg = xarML('This membership does not exist');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'ID_NOT_EXIST',
            new SystemException(__FILE__ . '(' . __LINE__ . '): ' . $msg));
        return;
*/
    }

    list($membershipid, $programid, $affiliateid, $program_key, $marketing_copy) = $result->fields;

    $result->Close();

    if (!xarSecurityCheck('ReadProgramMembership', 1, 'Membership', "All:All:$membershipid")) {
        return;
    }


	$item = array('membershipid' => $membershipid,
		'programid' => $programid,
		'affiliateid' => $affiliateid,
		'program_key' => $program_key,
		'marketing_copy' => $marketing_copy);

    return $item;
}

?>
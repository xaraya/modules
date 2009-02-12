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
function labaffiliate_affiliateapi_get($args)
{
    extract($args);

    if (!isset($affiliateid) || !is_numeric($affiliateid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
            'affiliate ID', 'user', 'get', 'labAffiliate');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
            new SystemException($msg));
        return;
    }

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

	$labaffiliate_affiliates_table = $xartable['labaffiliate_affiliates'];

    $query = "SELECT 
				xar_affiliateid,
				xar_uplineid,
				xar_userid,
				xar_primaryprogramid,
				xar_secondaryprogramid,
                xar_marketing_copy
              FROM $labaffiliate_affiliates_table
              WHERE xar_affiliateid = ?";
    $result = &$dbconn->Execute($query,array($affiliateid));

    if (!$result) return;

    if ($result->EOF) {
        $result->Close();
/*
        $msg = xarML('This affiliate does not exist');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'ID_NOT_EXIST',
            new SystemException(__FILE__ . '(' . __LINE__ . '): ' . $msg));
        return;
*/
        return 0;
    }

    list($affiliateid,$uplineid,$userid,$primaryprogramid,$secondaryprogramid,$marketing_copy) = $result->fields;

    $result->Close();

    if (!xarSecurityCheck('ReadProgramAffiliate', 1, 'Affiliate', "All:All:$affiliateid")) {
        return;
    }


	$item = array('affiliateid' => $affiliateid,
					'uplineid' => $uplineid,
					'userid' => $userid,
					'primaryprogramid' => $primaryprogramid,
					'secondaryprogramid' => $secondaryprogramid,
					'marketing_copy' => $marketing_copy);

    return $item;
}

?>
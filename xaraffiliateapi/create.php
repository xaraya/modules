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
function labaffiliate_affiliateapi_create($args)
{
    extract($args);

    // Argument check
    $invalid = array();

    if (!isset($uplineid)) {
        $invalid[] = 'Upline ID';
    }

    if (!isset($userid) || empty($userid)) {
        $invalid[] = 'User ID';
    }

    if (!isset($primaryprogramid)) {
        $invalid[] = 'Primary Program ID';
    }

    if (!isset($secondaryprogramid)) {
        $invalid[] = 'Secondary Program ID';
    }

    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
            join(', ', $invalid), 'affiliate', 'create', 'labAffiliate');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
            new SystemException($msg));
        return;
    }

    if (!xarSecurityCheck('AddProgramAffiliate', 1, 'Affiliate')) {
//        return; // re-add this once perms are straightened out...
    }

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

	$labaffiliate_affiliates_table = $xartable['labaffiliate_affiliates'];

    $nextId = $dbconn->GenId($labaffiliate_affiliates_table);


    $query = "INSERT INTO $labaffiliate_affiliates_table (
					xar_affiliateid,
					xar_uplineid,
					xar_userid,
					xar_primaryprogramid,
					xar_secondaryprogramid,
                    xar_marketing_copy
				)
	            VALUES (?,?,?,?,?,?)";

    $bindvars = array($nextId,$uplineid,$userid,$primaryprogramid,$secondaryprogramid,$marketing_copy);

    $result = &$dbconn->Execute($query,$bindvars);

    if (!$result) return;

    $affiliateid = $dbconn->PO_Insert_ID($labaffiliate_affiliates_table, 'xar_affiliateid');

    $item = $args;
    $item['module'] = 'labaffiliate';
    $item['itemid'] = $affiliateid;
    $item['itemtype'] = 2;
    xarModCallHooks('item', 'create', $affiliateid, $item);

    return $affiliateid;
}

?>

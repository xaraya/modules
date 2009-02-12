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
function labaffiliate_affiliateapi_update($args)
{
    extract($args);

    // Argument check
    $invalid = array();
    if (empty($affiliateid) || !is_numeric($affiliateid)) {
        $invalid[] = 'affiliate ID';
    }

    if (empty($primaryprogramid) || !is_numeric($primaryprogramid)) {
        $primaryprogramid = 0;
    }

    if (empty($secondaryprogramid) || !is_numeric($secondaryprogramid)) {
        $secondaryprogramid = 0;
    }

    if (empty($marketing_copy) || !is_string($marketing_copy)) {
        $marketing_copy = '';
    }

    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
            join(', ', $invalid), 'affiliate', 'update', 'labAffiliate');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
            new SystemException($msg));
        return;
    }

	$item = xarModAPIFunc('labaffiliate',
                		'affiliate',
                		'get',
                		array('affiliateid' => $affiliateid));
	/*Check for exceptions */
	if (!isset($item) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; // throw back

    if (!xarSecurityCheck('EditProgramAffiliate', 1, 'Affiliate', "All:All:$affiliateid")) {
        return;
    }

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

	$labaffiliate_affiliates_table = $xartable['labaffiliate_affiliates'];

	$query = "UPDATE $labaffiliate_affiliates_table
				SET
					xar_primaryprogramid =?,
					xar_secondaryprogramid =?,
                    xar_marketing_copy =?
				WHERE
					xar_affiliateid = ?";

    $bindvars = array((int) $primaryprogramid, (int) $secondaryprogramid, $marketing_copy, (int) $affiliateid);

	$result = &$dbconn->Execute($query,$bindvars);

	if (!$result) return;

    $item['module'] = 'labaffiliate';
    $item['itemid'] = $affiliateid;
    $item['itemtype'] = 2;
    xarModCallHooks('item', 'update', $affiliateid, $item);

	return true;

}

?>
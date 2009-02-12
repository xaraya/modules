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
function labaffiliate_affiliateapi_delete($args)
{
    extract($args);

    if (!isset($affiliateid) || !is_numeric($affiliateid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
            'affiliate ID', 'affiliate', 'delete', 'labaffiliate');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
            new SystemException($msg));
        return;
    }

    $item = xarModAPIFunc('labaffiliate',
        'affiliate',
        'get',
        array('affiliateid' => $affiliateid));
    /* Check for exceptions */
    if (!isset($item) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; /* throw back */

    if (!xarSecurityCheck('DeleteProgramAffiliate', 1, 'Affiliate', "All:All:$affiliateid")) {
        return;
    }

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

	$labaffiliate_affiliates_table = $xartable['labaffiliate_affiliates'];

    $query = "DELETE FROM $labaffiliate_affiliates_table WHERE xar_affiliateid = ?";

    $result = &$dbconn->Execute($query,array($affiliateid));

    if (!$result) return;

    $item['module'] = 'labaffiliate';
    $item['itemid'] = $affiliateid;
    $item['itemtype'] = 2;

    xarModCallHooks('item', 'delete', $affiliateid, $item);

    return true;
}

?>
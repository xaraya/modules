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
function labaffiliate_membershipapi_delete($args)
{
    extract($args);

    if (!isset($membershipid) || !is_numeric($membershipid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
            'membership ID', 'membership', 'delete', 'labAffiliate');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
            new SystemException($msg));
        return;
    }

    $item = xarModAPIFunc('labaffiliate',
        'membership',
        'get',
        array('membershipid' => $membershipid));
    /* Check for exceptions */
    if (!isset($item) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; /* throw back */

    if (!xarSecurityCheck('DeleteProgramMembership', 1, 'Membership', "All:All:$membershipid")) {
        return;
    }

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

	$labaffiliate_membership_table = $xartable['labaffiliate_membership'];

    $query = "DELETE FROM $labaffiliate_membership_table WHERE xar_membershipid = ?";

    $result = &$dbconn->Execute($query,array($membershipid));

    if (!$result) return;

    $item['module'] = 'labaffiliate';
    $item['itemid'] = $membershipid;
    $item['itemtype'] = 3;

    xarModCallHooks('item', 'delete', $membershipid, $item);

    return true;
}

?>
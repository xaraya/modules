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
function labaffiliate_adminapi_delete($args)
{
    extract($args);

    if (!isset($programid) || !is_numeric($programid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
            'program ID', 'admin', 'delete', 'labAffiliate');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
            new SystemException($msg));
        return;
    }

    $item = xarModAPIFunc('labaffiliate',
        'user',
        'get',
        array('programid' => $programid));
    /* Check for exceptions */
    if (!isset($item) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; /* throw back */

    if (!xarSecurityCheck('DeleteProgram', 1, 'Program', "$item[program_name]:All:$programid")) {
        return;
    }

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

	$labaffiliate_programs_table = $xartable['labaffiliate_programs'];

    $query = "DELETE FROM $labaffiliate_programs_table WHERE xar_programid = ?";

    $result = &$dbconn->Execute($query,array($programid));

    if (!$result) return;

    $item['module'] = 'labaffiliate';
    $item['itemid'] = $programid;
    $item['itemtype'] = 1;
    xarModCallHooks('item', 'delete', $programid, $item);

    return true;
}

?>
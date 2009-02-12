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
function labaffiliate_admin_modify($args)
{
    extract($args);

    if (!xarVarFetch('programid',     'id',     $programid,     $programid,     XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('program_name', 'str:1', $program_name, $program_name, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('programurl', 'str:1', $programurl, $programurl, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('affiliateurl', 'str:1', $affiliateurl, $affiliateurl, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('details', 'str:1', $details, $details, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('marketing_copy', 'str:1', $marketing_copy, $marketing_copy, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('invalid', 'array', $invalid, $invalid, XARVAR_NOT_REQUIRED)) return;

    if (!empty($objectid)) {
        $programid = $objectid;
    }

    $item = xarModAPIFunc('labaffiliate',
                          'user',
                          'get',
                          array('programid' => $programid));

    if (!isset($item) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; /* throw back */

    if (!xarSecurityCheck('EditProgram', 1, 'Program', "$item[program_name]:All:$programid")) {
        return;
    }

	$data = $item;

    $item['module'] = 'labaffiliate';
    $item['itemtype'] = 1;
    $hooks = xarModCallHooks('item', 'modify', $programid, $item);

    if (empty($hooks)) {
        $data['hookoutput'] = array();
    } else {
        $data['hookoutput'] = $hooks;
    }

	$data['authid'] = xarSecGenAuthKey();
	$data['invalid'] = $invalid;

    /* Return the template variables defined in this function */
    return $data;
}

?>

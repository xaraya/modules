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
function labaffiliate_admin_new($args)
{
    extract($args);

    if (!xarVarFetch('program_name',    'str:1:', $program_name,    $program_name,    XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('programurl', 'str:1',  $programurl, $programurl, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('affiliateurl', 'str:1',  $affiliateurl, $affiliateurl, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('details', 'str:1',  $details, $details, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('marketing_copy', 'str:1',  $marketing_copy, $marketing_copy, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('invalid', 'array',  $invalid, $invalid, XARVAR_NOT_REQUIRED)) return;

    if (!xarSecurityCheck('AddProgram')) return;

	$data = array();

    $data['authid'] = xarSecGenAuthKey();

    $item = array();
    $item['module'] = 'labaffiliate';
    $item['itemtype'] = 1;
    $hooks = xarModCallHooks('item', 'new', '', $item);

    if (empty($hooks)) {
        $data['hookoutput'] = array();
    } else {
        $data['hookoutput'] = $hooks;
    }
    $data['hooks'] = '';

    if (empty($program_name)) {
        $data['program_name'] = '';
    } else {
        $data['program_name'] = $program_name;
    }

    if (empty($programurl)) {
        $data['programurl'] = '';
    } else {
        $data['programurl'] = $programurl;
    }

    if (empty($affiliateurl)) {
        $data['affiliateurl'] = '';
    } else {
        $data['affiliateurl'] = $affiliateurl;
    }

    if (empty($details)) {
        $data['details'] = '';
    } else {
        $data['details'] = $details;
    }

    if (empty($marketing_copy)) {
        $data['marketing_copy'] = '';
    } else {
        $data['marketing_copy'] = $marketing_copy;
    }

    return $data;
}

?>

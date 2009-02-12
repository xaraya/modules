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
function labaffiliate_membership_new($args)
{
    extract($args);

    if (!xarVarFetch('affiliateid',  'id', $affiliateid,  $affiliateid,  XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('programid',  'id', $programid,  $programid,  XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('program_key',  'str:1:', $program_key,  $program_key,  XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('invalid', 'array',  $invalid, $invalid, XARVAR_NOT_REQUIRED)) return;

    if (!xarSecurityCheck('AddProgramAffiliate')) return;

	$invalid = array();

	$data = array();


    if (empty($programid)) {
        $data['programid'] = '';
    } else {
        $data['programid'] = $programid;
    }

    if (empty($affiliateid)) {
        $data['affiliateid'] = '';
    } else {
        $data['affiliateid'] = $affiliateid;
    }

    if (empty($program_key)) {
        $data['program_key'] = '';
    } else {
        $data['program_key'] = $program_key;
    }

    $data['authid'] = xarSecGenAuthKey();

    $item = array();
    $item['module'] = 'labaffiliate';
    $item['itemtype'] = 3;
    $hooks = xarModCallHooks('item', 'new', '', $item);

    if (empty($hooks)) {
        $data['hookoutput'] = array();
    } else {
        $data['hookoutput'] = $hooks;
    }
    $data['hooks'] = '';

	$programs = xarModAPIFunc('labaffiliate','user','getall');

	if (empty($programs) || !is_array($programs)) {
		$msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
			xarML('Programs no found'), 'membership', 'new', 'labAffiliate');
		xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
			new SystemException($msg));
		return;
	}

	$data['affiliateid'] = $affiliateid;
	$data['programid'] = $programid;
	$data['program_key'] = $program_key;
	$data['programs'] = $programs;

	$data['authid'] = xarSecGenAuthKey();
	$data['invalid'] = $invalid;

    return $data;
}

?>

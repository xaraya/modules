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
function labaffiliate_affiliate_new($args)
{
    extract($args);

    if (!xarVarFetch('programid',  'id', $programid,  $programid,  XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('uplineid',    'int:1:', $uplineid,    $uplineid,    XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('userid',    'int:1:', $userid,    $userid,    XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('primaryprogramid', 'int:1',  $primaryprogramid, $primaryprogramid, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('secondaryprogramid', 'int:1',  $secondaryprogramid, $secondaryprogramid, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('invalid', 'array',  $invalid, $invalid, XARVAR_NOT_REQUIRED)) return;

    if (!xarSecurityCheck('AddProgramAffiliate')) return;

	$invalid = array();

	$data = array();

    $data['authid'] = xarSecGenAuthKey();

    $item = array();
    $item['module'] = 'labaffiliate';
    $item['itemtype'] = 2;
    $hooks = xarModCallHooks('item', 'new', '', $item);

    if (empty($hooks)) {
        $data['hookoutput'] = array();
    } else {
        $data['hookoutput'] = $hooks;
    }
    $data['hooks'] = '';

	$programs = xarModAPIFunc('labaffiliate','user','getall');

	$numprograms = count($programs);

	$data['programid'] = $programid;
	$data['programs'] = $programs;

	$data['primaryprogramid'] = 0;
	$data['secondaryprogramid'] = 0;


	$data['numprograms'] = $numprograms;

	$data['authid'] = xarSecGenAuthKey();
	$data['invalid'] = $invalid;

    return $data;
}

?>

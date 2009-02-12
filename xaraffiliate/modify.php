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
function labaffiliate_affiliate_modify($args)
{
    extract($args);

    if (!xarVarFetch('affiliateid',     'id',     $affiliateid,     $affiliateid,     XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('objectid', 'id',     $objectid, $objectid, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('uplineid', 'id',     $uplineid, $uplineid, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('userid', 'id',     $userid, $userid, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('primaryprogramid', 'id',     $primaryprogramid, $primaryprogramid, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('secondaryprogramid', 'id',     $secondaryprogramid, $secondaryprogramid, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('invalid', 'array',  $invalid, $invalid, XARVAR_NOT_REQUIRED)) return;

    if (!empty($objectid)) {
        $affiliateid = $affiliateid;
    }

    $item = xarModAPIFunc('labaffiliate',
                          'affiliate',
                          'get',
                          array('affiliateid' => $affiliateid));

    if (!isset($item) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; /* throw back */

    if (!xarSecurityCheck('EditProgramAffiliate', 1, 'Affiliate', "All:All:$affiliateid")) {
        return;
    }

	$data = $item;
                            
//die(var_dump($item));
	$programs = xarModAPIFunc('labaffiliate','user','getall');

    if($programs === false) return;
    
	$data['programs'] = $programs;
    
	$numprograms = count($programs);

    $item['module'] = 'labaffiliate';
    $item['itemtype'] = 2;
    $hooks = xarModCallHooks('item', 'modify', $affiliateid, $item);

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

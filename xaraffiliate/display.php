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
function labaffiliate_affiliate_display($args)
{
    extract($args);

    if (!xarVarFetch('affiliateid', 'id', $affiliateid)) return;
    if (!xarVarFetch('objectid', 'id', $objectid, $objectid, XARVAR_NOT_REQUIRED)) return;

    if (!empty($objectid)) {
        $affiliateid = $objectid;
    }

    $item = xarModAPIFunc('labaffiliate',
        'affiliate',
        'get',
        array('affiliateid' => $affiliateid));
    if (!isset($item) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; /* throw back */

	if (!xarSecurityCheck('ReadProgramAffiliate',0,'Affiliate',"All:All:$item[affiliateid]")) {
		return;
	}

	$data = $item;
    
	$programlist = xarModAPIFunc('labaffiliate','user','getall');

    if($programlist === false) return;
    
	$data['programlist'] = $programlist;

	$primaryprogram = xarModAPIFunc('labaffiliate',
        'user',
        'get',
        array('programid' => $item['primaryprogramid']));

	$secondaryprogram = xarModAPIFunc('labaffiliate',
        'user',
        'get',
        array('programid' => $item['secondaryprogramid']));

    $program_memberships = xarModAPIFunc('labaffiliate', 'membership', 'getall', array('affiliateid' => $affiliateid));
    
    if($program_memberships === false) return;
    
    $data['program_memberships'] = $program_memberships;

    $item['itemid'] = $affiliateid;
    $item['itemtype'] = 2;
    xarModCallHooks('item', 'display', $affiliateid, $item);

    if (empty($hooks)) {
        $data['hookoutput'] = array();
    } else {
        $data['hookoutput'] = $hooks;
    }

	$data['primaryprogram'] = $primaryprogram;
	$data['secondaryprogram'] = $secondaryprogram;

    $data['hooks'] = '';

	return $data;
}

?>

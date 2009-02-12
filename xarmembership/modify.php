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
function labaffiliate_membership_modify($args)
{
    extract($args);

    if (!xarVarFetch('membershipid', 'id', $membershipid, $membershipid, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('affiliateid', 'id', $affiliateid, $affiliateid, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('objectid', 'id', $objectid, $objectid, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('programid', 'id',     $programid, $programid, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('affiliateid', 'id',     $affiliateid, $affiliateid, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('program_key', 'str:1:',     $program_key, $program_key, XARVAR_NOT_REQUIRED)) return;


    if (!xarVarFetch('invalid', 'array', $invalid, $invalid, XARVAR_NOT_REQUIRED)) return;

    if (!empty($objectid)) {
        $membershipid = $affiliateid;
    }

    $item = xarModAPIFunc('labaffiliate',
                          'membership',
                          'get',
                          array('membershipid' => $membershipid));

    if (!isset($item) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; /* throw back */

    if (!xarSecurityCheck('EditProgramMembership', 1, 'Membership', "All:All:$membershipid")) {
        return;
    }

	$data = $item;

	$memberships = xarModAPIFunc('labaffiliate','membership','getall',array('affiliateid' => $item['affiliateid']));

	$nummemberships = count($memberships);

    $data['affiliate'] = xarModAPIFunc('labaffiliate',
                          'affiliate',
                          'get',
                          array('affiliateid' => $item['affiliateid']));

    $item['module'] = 'labaffiliate';
    $item['itemtype'] = 3;
    $hooks = xarModCallHooks('item', 'modify', $membershipid, $item);

    if (empty($hooks)) {
        $data['hookoutput'] = array();
    } else {
        $data['hookoutput'] = $hooks;
    }

	$data['nummemberships'] = $nummemberships;

	$data['memberships'] = $memberships;

	$data['authid'] = xarSecGenAuthKey();
	$data['invalid'] = $invalid;

    /* Return the template variables defined in this function */
    return $data;
}

?>
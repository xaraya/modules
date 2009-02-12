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
function labaffiliate_user_display($args)
{
    extract($args);

    if (!xarVarFetch('programid', 'id', $programid)) return;
    if (!xarVarFetch('affiliateid', 'id', $affiliateid, NULL, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('objectid', 'id', $objectid, $objectid, XARVAR_NOT_REQUIRED)) return;

    if (!empty($objectid)) {
        $programid = $objectid;
    }

    $item = xarModAPIFunc('labaffiliate', 'user', 'get', array('programid' => $programid));
    
    if (!isset($item) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; /* throw back */

	$data = $item;

    $affiliateinfo = xarModAPIFunc('labaffiliate', 'affiliate', 'get', array('affiliateid' => $affiliateid));
    
    if($affiliateinfo === false) return;
    
    $data['affiliateinfo'] = $affiliateinfo;

    $membershipinfo = xarModAPIFunc('labaffiliate', 'membership', 'find', array('programid' => $programid, 'affiliateid' => $affiliateid));
    
    if($membershipinfo === false) return;
    
    $data['membershipinfo'] = $membershipinfo;
    
    if(!empty($membershipinfo['marketing_copy'])) {
        $data['marketing_copy'] = $membershipinfo['marketing_copy'];
    }

    $affiliates = xarModAPIFunc('labAffiliate',
                          'affiliate',
                          'getall',
                          array('programid' => $programid));


	$data['affiliates'] = $affiliates;

    $item['module'] = 'labaffiliate';
    $hooks = xarModCallHooks('item',
                            'display',
                            $programid,
                            $item);
    if (empty($hooks)) {
        $data['hookoutput'] = '';
    } else {
        $data['hookoutput'] = $hooks;
    }

    return $data;
}

?>
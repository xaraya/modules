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
function labaffiliate_admin_display($args)
{
    extract($args);

    if (!xarVarFetch('programid', 'id', $programid)) return;
    if (!xarVarFetch('objectid', 'id', $objectid, $objectid, XARVAR_NOT_REQUIRED)) return;

    if (!empty($objectid)) {
        $programid = $objectid;
    }

    $item = xarModAPIFunc('labaffiliate',
        'user',
        'get',
        array('programid' => $programid));
    if (!isset($item) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; /* throw back */

	if (!xarSecurityCheck('ReadProgram',0,'Program',"$item[program_name]:All:$item[programid]")) {
		return;
	}

	$data = $item;
	
	$affiliatelist = xarModAPIFunc('labaffiliate', 'membership', 'getall', array('programid' => $programid));
    
    if($affiliatelist === false) return;
    
    $data['affiliatelist'] = $affiliatelist;

    $item['itemid'] = $programid;
    $item['itemtype'] = 1;
    xarModCallHooks('item', 'display', $programid, $item);

    if (empty($hooks)) {
        $data['hookoutput'] = array();
    } else {
        $data['hookoutput'] = $hooks;
    }
    $data['hooks'] = '';

	return $data;
}

?>
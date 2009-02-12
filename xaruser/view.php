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
function labaffiliate_user_view()
{
    if (!xarVarFetch('startnum', 'int:1:', $startnum, 1, XARVAR_NOT_REQUIRED)) return;

    if (!xarSecurityCheck('ViewProgram')) return;

	$data = array();
	
    $data['pager'] = xarTplGetPager($startnum,
        xarModAPIFunc('labaffiliate', 'user', 'countitems'),
        xarModURL('labaffiliate', 'admin', 'view', array('startnum' => '%%')),
        xarModGetVar('labaffiliate', 'itemsperpage'));

    $items = xarModAPIFunc('labaffiliate',
                           'user',
                           'getall',
                            array('startnum' => $startnum,
                                  'numitems' => xarModGetVar('labaffiliate','itemsperpage')));
    /* Check for exceptions */
    if (!isset($items) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; /* throw back */

	$itemcount = count($items);

	$data['items'] = array();

    for ($i = 0; $i < $itemcount; $i++) {
    	if(!in_array($items[$i]['status'], array('Active'))){
    		continue;
    	}
        if (xarSecurityCheck('ReadProgram', 0, 'Program', "$items[$i][program_name]:All:$items[$i][programid]")) {
	        $data['items'][] = $items[$i];
        }
    }

    return $data;

}
?>
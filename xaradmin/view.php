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
function labaffiliate_admin_view()
{
    if (!xarVarFetch('startnum', 'int:1:', $startnum, 1, XARVAR_NOT_REQUIRED)) return;

    if (!xarSecurityCheck('EditProgram')) return;

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

    for ($i = 0; $i < $itemcount; $i++) {
        $item = $items[$i];
        if (xarSecurityCheck('EditProgram', 0, 'Program', "$item[program_name]:All:$item[programid]")) {
            $items[$i]['editurl'] = xarModURL('labaffiliate',
                'admin',
                'modify',
                array('programid' => $item['programid']));
        } else {
            $items[$i]['editurl'] = '';
        }
        if (xarSecurityCheck('DeleteProgram', 0, 'Program', "$item[program_name]:All:$item[programid]")) {
            $items[$i]['deleteurl'] = xarModURL('labaffiliate',
                'admin',
                'delete',
                array('programid' => $item['programid']));
        } else {
            $items[$i]['deleteurl'] = '';
        }
    }
    /* Add the array of items to the template variables */
    $data['items'] = $items;

    return $data;

}
?>
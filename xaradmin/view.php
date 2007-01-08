<?php
/**
 * View all education plans
 *
 * @package modules
 * @copyright (C) 2005-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage ITSP Module
 * @link http://xaraya.com/index.php/release/572.html
 * @author ITSP Module Development Team
 */
/**
 * Get and show all plans
 *
 * @author MichelV <michelv@xarayahosting.nl>
 * @param int startnum
 * @return array
 */
function itsp_admin_view()
{
    if (!xarVarFetch('startnum', 'int:1:', $startnum, 1, XARVAR_NOT_REQUIRED)) return;

    $data = xarModAPIFunc('itsp', 'admin', 'menu');
    /* Initialise the variable that will hold the items, so that the template
     * doesn't need to be adapted in case of errors
     */
    $data['items'] = array();

    /* Call the xarTPL helper function to produce a pager in case of there
     * being many items to display.
     */
    $data['pager'] = xarTplGetPager($startnum,
        xarModAPIFunc('itsp', 'user', 'countitems', array('itemtype' => 1)),
        xarModURL('itsp', 'admin', 'view', array('startnum' => '%%')),
        xarModGetVar('itsp', 'itemsperpage'));
    /* Security check - important to do this as early as possible to avoid
     * potential security holes or just too much wasted processing
     */
    if (!xarSecurityCheck('EditITSP')) return;

    $items = xarModAPIFunc('itsp',
                           'user',
                           'getall_plans',
                            array('startnum' => $startnum,
                                  'numitems' => xarModGetVar('itsp','itemsperpage')));
    /* Check for exceptions */
    if (!isset($item) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; /* throw back */

    /* Check individual permissions for Edit / Delete
     * Note : we could use a foreach ($items as $item) here as well, as
     * shown in xaruser.php, but as an itsp, we'll adapt the $items array
     * 'in place', and *then* pass the complete items array to $data
     */
    $planid = '';
    for ($i = 0; $i < count($items); $i++) {
        $item = $items[$i];
        $planid = $item['planid'];
        if (xarSecurityCheck('EditITSPPlan', 0, 'Plan', "$planid:All")) {
            $items[$i]['editurl'] = xarModURL('itsp',
                'admin',
                'modify',
                array('planid' => $planid));
        } else {
            $items[$i]['editurl'] = '';
        }
        if (xarSecurityCheck('DeleteITSPPlan', 0, 'Plan', "$planid:All")) {
            $items[$i]['deleteurl'] = xarModURL('itsp',
                'admin',
                'delete',
                array('planid' => $planid));
        } else {
            $items[$i]['deleteurl'] = '';
        }
        if (xarSecurityCheck('AddITSPPlan', 0, 'Plan', "$planid:All")) {
            $items[$i]['pitemurl'] = xarModURL('itsp',
                'admin',
                'new_pitem',
                array('planid' => $planid));
        } else {
            $items[$i]['pitemurl'] = '';
        }
    }
    /* Add the array of items to the template variables */
    $data['items'] = $items;

    if (xarSecurityCheck('AddITSPPlan', 0, 'Plan')) {
        $data['addurl'] = xarModURL('itsp','admin','new');
    } else {
        $data['addurl'] = '';
    }
    /* Return the template variables defined in this function */
    return $data;
}
?>
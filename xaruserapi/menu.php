<?php
/**
 * Generate the common menu configuration
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage ITSP Module
 * @link http://xaraya.com/index.php/release/572.html
 * @author ITSP Module Development Team
 */
/**
 * Generate the common menu configuration
 *
 * @author the ITSP module development team
 */
function itsp_userapi_menu()
{
    /* Initialise the array that will hold the menu configuration */
    $menu = array();

    /* Specify the menu title to be used in your blocklayout template */
    $menu['menutitle'] = xarML('Individual Training and Supervision Plan');

    /* Specify the menu items to be used in your blocklayout template */
    $menu['menulabel_view'] = xarML('View ITSP');
    $menu['menulink_view'] = xarModURL('itsp', 'user', 'view');

    // Specify the labels/links for more menu items if relevant

    // Get the planitems that a user uses in his ITSP
    $userid = xarUserGetVar('uid');
    $itsp = xarModApiFunc('itsp','user','get',array('userid'=>$userid));
    $planid = $itsp['planid'];

    $pitems = xarModApiFunc('itsp','user','get_planitems',array('planid'=>$planid));

    $menu['pitemnames'] = array();
/* Enter items*/
    foreach ($pitems as $item) {
        // Add read link
        $planid = $item['planid'];
        if (xarSecurityCheck('ReadITSPPlan', 0, 'Plan', "$planid:All:All")) {
            $item['link'] = xarModURL('itsp',
                'user',
                'display',
                array('planid' => $planid));

        } else {
            $item['link'] = '';
        }
        $pitemname = xarModApiFunc('itsp','user','get_planitem',array('pitemid'=>$item['pitemid']));
        $item['pitemname'] = xarVarPrepForDisplay($item['pitemname']);
        $menu['pitems'][] = $item;
    }



     /* Return the array containing the menu configuration */
    return $menu;
}
?>
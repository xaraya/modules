<?php
/**
 * Generate the common menu configuration
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage ITSP Module
 * @link http://xaraya.com/index.php/release/572.html
 * @author ITSP Module Development Team
 */
/**
 * Generate the common user menu configuration
 *
 * This menu is used in the ITSP area for users
 *
 * @author the ITSP module development team
 */
function itsp_userapi_menu()
{
    /* Initialise the array that will hold the menu configuration */
    $menu = array();
    if (!xarVarFetch('itspid',   'id', $itspid,   NULL, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('pitemid',  'id', $pitemid,  NULL, XARVAR_NOT_REQUIRED)) return;
    /* Specify the menu title to be used in your blocklayout template */
    $menu['menutitle'] = xarML('Individual Training and Supervision Plan');

    // Specify the labels/links for more menu items if relevant

    // Get the planitems that a user must use in his ITSP
    $userid = xarUserGetVar('uid');
    $itsp = xarModApiFunc('itsp','user','get',array('userid'=>$userid));
    if(!empty($itsp)) {
        $planid = $itsp['planid'];
        $itspid = $itsp['itspid'];
        $menu['itspid'] = $itspid;
        $menu['planid'] = $planid;
        // Get the planitems for this plan in the ITSP
        $pitems = xarModApiFunc('itsp','user','get_planitems',array('planid'=>$planid));
        if (!empty($pitems)) {
            $menu['pitemnames'] = array();
            /* Enter items*/
            foreach ($pitems as $item) {
                // Add modify link
                $pitemid= $item['pitemid'];
                $pitem = xarModApiFunc('itsp','user','get_planitem',array('pitemid'=>$pitemid));

                if (xarSecurityCheck('EditITSPPlan', 0, 'Plan', "$planid:$pitemid:All")) {
                    $item['link'] = xarModURL('itsp',
                        'user',
                        'modify',
                        array('pitemid' => $pitemid, 'itspid' => $itspid));

                } else {
                    $item['link'] = '';
                }

                $item['pitemname'] = xarVarPrepForDisplay($pitem['pitemname']);
                $menu['pitems'][] = $item;
            }
        }
    }

     /* Return the array containing the menu configuration */
    return $menu;
}
?>
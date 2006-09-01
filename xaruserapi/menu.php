<?php
/**
 * Generate the common menu configuration
 *
 * @package modules
 * @copyright (C) 2005-2006 The Digital Development Foundation
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
 * This menu is used in the ITSP area for users. It gets
 * the full itsp and the values from the menu can be considered the full ITSP
 *
 * @author MichelV <michelv@xarayahosting.nl>
 * @param id itspid
 * @param id pitemid
 * @return array
 */
function itsp_userapi_menu()
{
    /* Initialise the array that will hold the menu configuration */
    $menu = array();
    if (!xarVarFetch('itspid',   'id', $itspid,   NULL, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('pitemid',  'id', $pitemid,  NULL, XARVAR_NOT_REQUIRED)) return;
    /* Specify the menu title to be used in your blocklayout template */
    $menu['menutitle'] = xarML('Individual Training and Supervision Plan');

    $userid = xarUserGetVar('uid');
    // If there is no itspid specified, then assume we want to see the current user's ITSP
    if (!$itspid) {
        $where = "xar_userid = $userid";
    } else {
        $where = "xar_itspid = $itspid";
    }
    //Get ITSP
    //Better ignore error
    //$itsp = xarModApiFunc('itsp','user','get',array('userid'=>$userid));

    // Get database setup
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $itsptable = $xartable['itsp_itsp'];
    // Get item by userid
    // TODO: move to own api?
    $query = "SELECT xar_itspid,
                     xar_planid
              FROM $itsptable
              WHERE $where";

    $result = &$dbconn->Execute($query);
    /* Check for an error with the database code, adodb has already raised
     * the exception so we just return
     */
    if (!$result) return $menu;

    /* Obtain the item information from the result set */
    list($itspid, $planid) = $result->fields;
    $result->Close();

    if(!empty($itspid)) {
        //$planid = $itsp['planid'];
        //$itspid = $itsp['itspid'];
        $menu['itspid'] = $itspid;
        $menu['planid'] = $planid;
        // Get the planitems for this plan in the ITSP
        $pitems = xarModApiFunc('itsp','user','get_planitems',array('planid'=>$planid));
        if (!empty($pitems)) {
            $menu['pitemnames'] = array();
            /* Enter items*/
            $sumcreditsnow = 0;
            foreach ($pitems as $item) {
                // Add modify link
                $pitemid= $item['pitemid'];
                $pitem = xarModApiFunc('itsp','user','get_planitem',array('pitemid'=>$pitemid));

        //        if (xarSecurityCheck('EditITSPPlan', 0, 'Plan', "$planid:$pitemid")) {
                    $item['link'] = xarModURL('itsp',
                        'user',
                        'modify',
                        array('pitemid' => $pitemid, 'itspid' => $itspid));

       /*         } else {
                    $item['link'] = '';
                }
       */
                // Add credits so we can do calculations
                $item['mincredit'] = $pitem['mincredit'];
                $item['credits'] = $pitem['credits'];
                $item['pitemid']=$pitemid;
                $creditsnow = xarModApiFunc('itsp','user','countcredits',array('uid' => $userid, 'pitemid' => $pitemid, 'itspid' => $itspid));
                $item['creditsnow'] = $creditsnow;
                $sumcreditsnow = $sumcreditsnow + $creditsnow;
                // Format the name
                $item['pitemname'] = xarVarPrepForDisplay($pitem['pitemname']);
                $menu['pitems'][] = $item;
            }
            $menu['sumcreditsnow'] = $sumcreditsnow;
        }
        xarVarSetCached('pitems.itsp', 'pitems', $menu['pitems']);
    }

     /* Return the array containing the menu configuration */
    return $menu;
}
?>
<?php
/**
 * Display the ITSP for one user
 *
 * @package modules
 * @copyright (C) 2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage ITSP Module
 * @link http://xaraya.com/index.php/release/572.html
 * @author ITSP Module Development Team
 */
/**
 * Display the user's ITSP
 *
 * Show the user the full details of the plan chosen, and the status of all items.
 *
 * @author the ITSP module development team
 * @param  $args an array of arguments (if called by other modules)
 * @param  $args ['objectid'] a generic object id (if called by other modules)
 * @param  $args ['itspid'] the item id used for this itsp module
 * @since 1 Sept 2006
 * @return array
 */
function itsp_user_review($args)
{
    // Quick one
    if(!xarSecurityCheck('ViewITSP')) return;
    extract($args);

    if (!xarVarFetch('itspid',   'id', $itspid,   NULL, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('pitemid',  'id', $pitemid,  NULL, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('objectid', 'id', $objectid, $objectid, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('userid',   'id', $userid,   NULL, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('fulldetails', 'checkbox', $fulldetails, false, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('startnum', 'int:1:', $startnum, 1, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('statusselect', 'int:0:', $statusselect, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('username', 'str:1:', $getname, '', XARVAR_NOT_REQUIRED)) return;
    /* At this stage we check to see if we have been passed $objectid, the
     * generic item identifier.
     */    /*
    if (!empty($objectid)) {
        $itspid = $objectid;
    }

    // We have a valid ITSP?
    if (!empty($userid)) {
        $item = xarModAPIFunc('itsp',
                          'user',
                          'get_itspid',
                          array('userid' => $userid));
        if (!empty($item)) {
            xarResponseRedirect(xarModURL('itsp', 'user', 'itsp', array('itpsid'=>$item['itspid'], 'pitemid'=> $pitemid)));
        }
    } elseif (!empty($itspid)) {
        // The user API function is called to get the ITSP
        $item = xarModAPIFunc('itsp',
                              'user',
                              'get',
                              array('itspid' => $itspid));
        if (!empty($item)) {
            xarResponseRedirect(xarModURL('itsp', 'user', 'itsp', array('itpsid'=>$itspid, 'pitemid'=> $pitemid)));
        }
    }*/
/*
    if (empty($item)) {
        xarTplSetPageTitle(xarML('Individual Training and Supervision Plan'));
        $data = xarModAPIFunc('itsp', 'user', 'menu');
        return $data;
    }
    */

    $data = array();
    $uid = xarUserGetVar('uid');
    // Get all the ITSPs and set their status
    $data['items'] = array();

    if (empty($getname)) {
        $items = xarModAPIFunc('itsp',
                              'user',
                              'getall',
                              array('startnum' => $startnum,
                                    'numitems' => xarModGetUserVar('itsp','itemsperpage', $uid),
                                    'statusselect' => $statusselect));
    } else {
        $items = xarModAPIFunc('itsp',
                              'user',
                              'getall',
                              array('startnum' => $startnum,
                                    'numitems' => -1));
    }
    if (!isset($items) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; // throw back

    // Check status
    $stati = xarModApiFunc('itsp','user','getstatusinfo');
    $data['stati'] = $stati;
    $data['statusselect'] = $statusselect;
    if (!empty($getname)) {
        $getname = (string)$getname;
    }
    $data['username'] = $getname;

    if(!empty($items)) {
        foreach ($items as $item) {
            $itspid = $item['itspid'];
            $userid = $item['userid'];
            $planid = $item['planid'];
            $item['username'] = xarUserGetVar('name', $userid);
            if(!empty($getname)) {
                if (stristr($item['username'], $getname) === false) {
                    continue ;
                }
            }

            // Add read link
            if (xarSecurityCheck('ReadITSP', 0, 'ITSP', "$itspid:$planid:$userid")) {
                $item['itsplink'] = xarModURL('itsp',
                    'user',
                    'itsp',
                    array('itspid' => $itspid));
                /* Security check 2 - else only display the item name (or whatever is
                 * appropriate for your module)
                 */
            } else {
                $item['itsplink'] = '';
            }

            $item['rolelink'] = xarModURL('roles',
                    'user',
                    'display',
                    array('uid' => $userid));
            $itspstatus = $item['itspstatus'];
            $item['statusname'] = xarVarPrepForDisplay($stati[$itspstatus]);

            // TODO: Total credits
            // TODO: all link to change status
            $data['items'][] = $item;
        }
    }
    // Security
    $data['authid'] = xarSecGenAuthKey();
    $data['fulldetails'] = $fulldetails;

    if (empty($getname)) {
    $data['pager'] = xarTplGetPager($startnum,
        xarModAPIFunc('itsp', 'user', 'countitems', array('itemtype' => 2, 'itspstatus' =>$statusselect)),
        xarModURL('itsp', 'user', 'review', array('startnum' => '%%', 'statusselect' => $statusselect)),
        xarModGetUserVar('itsp', 'itemsperpage', $uid));
    } else {
        $data['pager'] = '';
    }

    /* Once again, we are changing the name of the title for better
     * search engine capability.*/

    xarTplSetPageTitle(xarML('Review ITSPs'));
    /* Return the template variables defined in this function */
    return $data;
}
?>
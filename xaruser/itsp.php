<?php
/**
 * Display the ITSP for one user
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
 * Display the user's ITSP
 *
 * Show the user the full details of the plan chosen, and the status of all items.
 *
 * @author the ITSP module development team
 * @param  $args an array of arguments (if called by other modules)
 * @param  $args ['objectid'] a generic object id (if called by other modules)
 * @param  $args ['itspid'] the item id used for this itsp module
 * @return array
 */
function itsp_user_itsp($args)
{
    // Quick one
    if(!xarSecurityCheck('ViewITSP')) return;
    extract($args);

    if (!xarVarFetch('itspid',   'id', $itspid,   NULL, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('pitemid',  'id', $pitemid,  NULL, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('objectid', 'id', $objectid, $objectid, XARVAR_NOT_REQUIRED)) return;

    /* At this stage we check to see if we have been passed $objectid, the
     * generic item identifier.
     */
    if (!empty($objectid)) {
        $itspid = $objectid;
    }
    /* Add the ITSP user menu */
    // This also gets already all the planitems...
    $data = xarModAPIFunc('itsp', 'user', 'menu');

    // We have a valid ITSP?
    if (empty($itspid)) {
        $item = xarModAPIFunc('itsp',
                          'user',
                          'get_itspid',
                          array('userid' => xarUserGetVar('uid')));
    } else {

        // The user API function is called to get the ITSP
        $item = xarModAPIFunc('itsp',
                              'user',
                              'get',
                              array('itspid' => $itspid));
    }

    if (empty($item)) {
        xarTplSetPageTitle(xarML('Individual Training and Supervision Plan'));
        return $data;
    }

    $data['itspid'] = $item['itspid'];
    // First see if there is an id to get.
    // Check status
    $stati = xarModApiFunc('itsp','user','getstatusinfo');
    $data['stati'] = $stati;
    $itspstatus = $item['itspstatus'];
    $data['statusname'] = $stati[$itspstatus];
    $data['itspstatus'] = $itspstatus;

    $planid = $item['planid'];
    $userid = $item['userid'];
    // Security check
    if (!xarSecurityCheck('ReadITSP',1,'ITSP',"$itspid:$planid:$userid")) {
       return $data;
    }

    $item['itemtype'] = 2;
    // Add the ITSP
    $data['item'] = $item;
    // Get the plan
    $plan = xarModApiFunc('itsp','user','get_plan',array('planid' => $planid));
    if (empty($plan) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; // throw back
    $data['plan'] = $plan;
    // Security
    $data['authid'] = xarSecGenAuthKey();

    xarVarSetCached('Blocks.itsp', 'itspid', $itspid);
    /* Let any hooks know that we are displaying an item.
     */
    $item['returnurl'] = xarModURL('itsp',
        'user',
        'display',
       array('itspid' => $itspid));
    $hooks = xarModCallHooks('item',
        'display',
        $itspid,
        $item);
    if (empty($hooks)) {
        $data['hookoutput'] = array();
    } else {
        $data['hookoutput'] = $hooks;
    }
    /* Once again, we are changing the name of the title for better
     * Search engine capability.
     */
    xarTplSetPageTitle(xarVarPrepForDisplay($item['itspid']));
    /* Return the template variables defined in this function */
    return $data;
}
?>
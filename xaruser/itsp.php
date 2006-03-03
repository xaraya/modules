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
                              'get_itspid',
                              array('itspid' => $itspid));
    }
    $data['itspid'] = $itspid;
    // First see if there is an id to get.

    if (empty($item)) {
        xarTplSetPageTitle(xarML('Individual Training and Supervision Plan'));
        return $data;
    }
    $planid = $item['planid'];
    // Security check
    if (!xarSecurityCheck('ReadITSP',0,'ITSP',"$itspid:$planid:All")) {
       return $data;
    }

    $item['itemtype'] = 2;
    // Add the ITSP
    $data['item'] = $item;
    // Get the plan
    $plan = xarModApiFunc('itsp','user','get_plan',array('planid' => $planid));
    if (empty($plan) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; // throw back
    $data['plan'] = $plan;
    // Get the planitems for this plan in the ITSP
    //$pitems = xarModApiFunc('itsp','user','get_planitems',array('planid'=>$planid));
    // These are already in $data


    // Do this in the menu
    // $creditsnow = xarModApiFunc('itsp','user','countcredits',array('uid' => xarUserGetVar('uid')));
 //   xarSessionSetVar('statusmsg', xarML('Course Item was successfully added!'));

    /* Let any transformation hooks know that we want to transform some text.
     * You'll need to specify the item id, and an array containing the names of all
     * the pieces of text that you want to transform (e.g. for autolinks, wiki,
     * smilies, bbcode, ...).

    $item['transform'] = array('name');
    $item = xarModCallHooks('item','transform', $itspid, $item);
    // Fill in the details of the item.
    $data['name_value'] = $item['name'];
    $data['number_value'] = $item['number'];
*/
    //$data['is_bold'] = xarModGetVar('itsp', 'bold');

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
<?php
 /**
 * Display an item
 *
 * @package modules
 * @copyright (C) 2005-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Maxercalls module
 * @author Michel Vorenhout
 */
/**
 * display an item
 * This is a standard function to provide detailed informtion on a single item
 * available from the module.
 *
 * @param  $args an array of arguments (if called by other modules)
 * @param  $args ['objectid'] a generic object id (if called by other modules)
 * @param  $args ['callid'] the item id used for this maxercalls module
 */
function maxercalls_user_display($args)
{
    extract($args);

    if (!xarVarFetch('callid', 'int:1:', $callid)) return;
    if (!xarVarFetch('objectid', 'str:1:', $objectid, '', XARVAR_NOT_REQUIRED)) return;

    if (!empty($objectid)) {
        $callid = $objectid;
    }
    // Initialise the $data variable
    $data = xarModAPIFunc('maxercalls', 'user', 'menu');
    // Prepare the variable that will hold some status message if necessary
    $data['status'] = '';
    // Get the call
    $item = xarModAPIFunc('maxercalls',
        'user',
        'get',
        array('callid' => $callid));
    if (!isset($item) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; // throw back
    $item['module'] = 'maxercalls';
    $item['transform'] = array('name');
    $item['itemtype'] =1;
    $item = xarModCallHooks('item',
        'transform',
        $callid,
        $item);
    // Fill in the details of the item.  Note that a module variable is used here to determine
    // whether or not parts of the item information should be displayed in
    // bold type or not
    $data['calldatelabel'] = xarVarPrepForDisplay(xarML('Date of call'));
    $data['calltimelabel'] = xarVarPrepForDisplay(xarML('Time of call'));
    $data['calltextlabel'] = xarVarPrepForDisplay(xarML('Text of call'));
    $data['entertslabel'] = xarVarPrepForDisplay(xarML('Entered on'));
    $data['enteredbylabel'] = xarVarPrepForDisplay(xarML('Entered by'));
    $data['categorylabel'] = xarVarPrepForDisplay(xarML('Category of call'));
    $data['remarkslabel'] = xarVarPrepForDisplay(xarML('Remarks'));
    $data['calltextlabel'] = xarVarPrepForDisplay(xarML('Text of the call'));
    $data['calltime_value'] = $item['calltime'];
    $data['enterts_value'] = $item['enterts'];
    $data['enteredby_value'] = xarUserGetVar('name', $item['enteruid']);
    $data['remarks_value'] = $item['remarks'];
    $data['callid'] = $callid;
    $data['returnbutton'] = xarVarPrepForDisplay(xarML('Return to view'));
    $data['calltext'] = xarModAPIFunc('dynamicdata','user','getfield',
                    array ('module' => 'maxercalls',
                           'itemtype' => 3,
                           'itemid' => $item['calltext'],
                           'name' => 'calltext'));

    //Build date to display
    $date_elements  = explode("-",$item['calldate']);
    $callyear = $date_elements[0];
    $callmonth = $date_elements[1];
    $callday = $date_elements[2];
    $calldatedisplay = $callday."-".$callmonth."-".$callyear;
    $data['calldate_value'] = $calldatedisplay;

    // You should use this -instead of globals- if you want to make
    // information available elsewhere in the processing of this page request
    xarVarSetCached('Blocks.maxercalls', 'callid', $callid);
    // Let any hooks know that we are displaying an item.
    $item['returnurl'] = xarModURL('maxercalls',
        'user',
        'display',
        array('callid' => $callid));
    $hooks = xarModCallHooks('item',
        'display',
        $callid,
        $item);
    if (empty($hooks)) {
        $data['hookoutput'] = array();
    } else {
        // You can use the output from individual hooks in your template too, e.g. with
        // $hookoutput['comments'], $hookoutput['hitcount'], $hookoutput['ratings'] etc.
        $data['hookoutput'] = $hooks;
    }
    // Once again, we are changing the name of the title for better
    // Search engine capability.
    xarTplSetPageTitle(xarVarPrepForDisplay($item['calldate']));
    // Return the template variables defined in this function
    return $data;
}
?>
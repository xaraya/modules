<?php
/**
 * File: $Id:
 * 
 * Standard function to view items
 * 
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage userpoints
 * @author Userpoints module development team 
 */
/**
 * view items
 */
function userpoints_admin_viewrank()
{ 
    // Get parameters from whatever input we need.  All arguments to this
    // function should be obtained from xarVarFetch(), xarVarCleanFromInput()
    // is a degraded function.  xarVarFetch allows the checking of the input
    // variables as well as setting default values if needed.  Getting vars
    // from other places such as the environment is not allowed, as that makes
    // assumptions that will not hold in future versions of Xaraya
    if (!xarVarFetch('startnum', 'str:1:', $startnum, '1', XARVAR_NOT_REQUIRED)) return; 
    // Initialise the $data variable that will hold the data to be used in
    // the blocklayout template, and get the common menu configuration - it
    // helps if all of the module pages have a standard menu at the top to
    // support easy navigation
    // Initialise the variable that will hold the items, so that the template
    // doesn't need to be adapted in case of errors
    $data['items'] = array(); 
    // Specify some labels for display
    $data['ranknamelabel'] = xarVarPrepForDisplay(xarML('Rank Name'));
    $data['rankminscorelabel'] = xarVarPrepForDisplay(xarML('Rank Minimum Score'));
    $data['optionslabel'] = xarVarPrepForDisplay(xarML('Options')); 
    // Call the xarTPL helper function to produce a pager in case of there
    // being many items to display.
    
    // Note that this function includes another user API function.  The
    // function returns a simple count of the total number of items in the item
    // table so that the pager function can do its job properly
    $data['pager'] = xarTplGetPager($startnum,
        xarModAPIFunc('userpoints', 'user', 'countranks'),
        xarModURL('userpoints', 'admin', 'viewrank', array('startnum' => '%%')),
        xarModGetVar('userpoints', 'ranksperpage')); 
    // Security check - important to do this as early as possible to avoid
    // potential security holes or just too much wasted processing
    if (!xarSecurityCheck('AdminRank')) return; 
    // The user API function is called.  This takes the number of items
    // required and the first number in the list of all items, which we
    // obtained from the input and gets us the information on the appropriate
    // items.
    $items = xarModAPIFunc('userpoints',
                           'user',
                           'getallranks',
        array('startnum' => $startnum,
            'numitems' => xarModGetVar('userpoints',
                                       'ranksperpage'))); 
    // Check for exceptions
    if (!isset($item) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; // throw back
     
    // Check individual permissions for Edit / Delete
    // Note : we could use a foreach ($items as $item) here as well, as
    // shown in xaruser.php, but as an example, we'll adapt the $items array
    // 'in place', and *then* pass the complete items array to $data
    for ($i = 0; $i < count($items); $i++) {
        $item = $items[$i];
        if (xarSecurityCheck('AdminRank', 0, 'Item', "$item[rankname]:All:$item[id]")) {
            $items[$i]['editurl'] = xarModURL('userpoints',
                'admin',
                'modifyrank',
                array('id' => $item['id']));
        } else {
            $items[$i]['editurl'] = '';
        } 
        $items[$i]['edittitle'] = xarML('Edit');
        if (xarSecurityCheck('DeleteRank', 0, 'Item', "$item[rankname]:All:$item[id]")) {
            $items[$i]['deleteurl'] = xarModURL('userpoints',
                'admin',
                'deleterank',
                array('id' => $item['id']));
        } else {
            $items[$i]['deleteurl'] = '';
        } 
        $items[$i]['deletetitle'] = xarML('Delete');
    } 
    // Add the array of items to the template variables
    $data['items'] = $items; 
    // Specify some labels for display
    $data['ranknamelabel'] = xarVarPrepForDisplay(xarML('Rank Name'));
    $data['rankminscorelabel'] = xarVarPrepForDisplay(xarML('Rank Minimum Score'));
    $data['optionslabel'] = xarVarPrepForDisplay(xarML('Options')); 
    // Return the template variables defined in this function
    return $data; 
    // Note : instead of using the $data variable, you could also specify
    // the different template variables directly in your return statement :
    
    // return array('items' => ...,
    // 'namelabel' => ...,
    // ... => ...);
} 

?>

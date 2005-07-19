<?php

/**
 * view items
 */
function events_admin_view()
{
    // Get parameters from whatever input we need.  All arguments to this
    // function should be obtained from xarVarCleanFromInput(), getting them
    // from other places such as the environment is not allowed, as that makes
    // assumptions that will not hold in future versions of Xaraya
    $startnum = xarVarCleanFromInput('startnum');

    // Initialise the $data variable that will hold the data to be used in
    // the blocklayout template, and get the common menu configuration - it
    // helps if all of the module pages have a standard menu at the top to
    // support easy navigation
    $data = xarModAPIFunc('events', 'admin', 'menu');

    // Initialise the variable that will hold the items, so that the template
    // doesn't need to be adapted in case of errors
    $data['items'] = array();

    // Specify some labels for display
    $data['namelabel'] = xarVarPrepForDisplay(xarML('EXAMPLENAME'));
    $data['numberlabel'] = xarVarPrepForDisplay(xarML('EXAMPLENUMBER'));
    $data['optionslabel'] = xarVarPrepForDisplay(xarML('EXAMPLEOPTIONS'));

    // Call the xarTPL helper function to produce a pager in case of there
    // being many items to display.
    //
    // Note that this function includes another user API function.  The
    // function returns a simple count of the total number of items in the item
    // table so that the pager function can do its job properly
    $data['pager'] = xarTplGetPager($startnum,
                                    xarModAPIFunc('events', 'user', 'countitems'),
                                    xarModURL('events', 'admin', 'view', array('startnum' => '%%')),
                                    xarModGetVar('events', 'itemsperpage'));

    // Security check - important to do this as early as possible to avoid
    // potential security holes or just too much wasted processing
	if(!xarSecurityCheck('EditEvents')) return;

    // The user API function is called.  This takes the number of items
    // required and the first number in the list of all items, which we
    // obtained from the input and gets us the information on the appropriate
    // items.
    $items = xarModAPIFunc('events',
                          'user',
                          'getall',
                          array('startnum' => $startnum,
                                'numitems' => xarModGetVar('events',
                                                          'itemsperpage')));
    // Check for exceptions
    if (!isset($item) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; // throw back

    // Check individual permissions for Edit / Delete
    // Note : we could use a foreach ($items as $item) here as well, as
    // shown in xaruser.php, but as an events, we'll adapt the $items array
    // 'in place', and *then* pass the complete items array to $data
    for ($i = 0; $i < count($items); $i++) {
        $item = $items[$i];
		    if(xarSecurityCheck('EditEvents',0,'All',"$item[name]:All:$item[exid]")){
            $items[$i]['editurl'] = xarModURL('events',
                                             'admin',
                                             'modify',
                                             array('exid' => $item['exid']));
        } else {
            $items[$i]['editurl'] = '';
        }
        $items[$i]['edittitle'] = xarML('Edit');
		if(xarSecurityCheck('DeleteEvents',0,'All',"$item[name]:All:$item[exid]")){
            $items[$i]['deleteurl'] = xarModURL('events',
                                               'admin',
                                               'delete',
                                               array('exid' => $item['exid']));
        } else {
            $items[$i]['deleteurl'] = '';
        }
        $items[$i]['deletetitle'] = xarML('Delete');
    }

    // Add the array of items to the template variables
    $data['items'] = $items;

    // Specify some labels for display
    $data['namelabel'] = xarVarPrepForDisplay(xarML('EXAMPLENAME'));
    $data['numberlabel'] = xarVarPrepForDisplay(xarML('EXAMPLENUMBER'));
    $data['optionslabel'] = xarVarPrepForDisplay(xarML('EXAMPLEOPTIONS'));

    // Return the template variables defined in this function
    return $data;

    // Note : instead of using the $data variable, you could also specify
    // the different template variables directly in your return statement :
    //
    // return array('items' => ...,
    //              'namelabel' => ...,
    //              ... => ...);
}

?>
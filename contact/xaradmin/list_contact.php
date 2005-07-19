<?php

/**
 * view items
 */
function contact_admin_list_contact()
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
    $data = xarModAPIFunc('contact','admin','menu');

    // Initialise the variable that will hold the items, so that the template
    // doesn't need to be adapted in case of errors
    $data['items'] = array();

    // Specify some labels for display
    $data['firstname_label'] = xarVarPrepForDisplay(xarML('Firstname'));
    $data['lastname_label'] = xarVarPrepForDisplay(xarML('Lastname'));
    $data['email_label'] = xarVarPrepForDisplay(xarML('Email'));
    $data['departments_label'] = xarVarPrepForDisplay(xarML('Departments'));
    $data['delete_label'] = xarVarPrepForDisplay(xarML('Delete'));
    $data['addcontactbutton'] = xarVarPrepForDisplay(xarML('Commit'));
    $data['pager'] = '';

     // Get parameters from whatever input we need.  All arguments to this
    // function should be obtained from xarVarCleanFromInput(), getting them
    // from other places such as the environment is not allowed, as that makes
    // assumptions that will not hold in future versions of Xaraya
    list($id,
         $department) = xarVarCleanFromInput('id',
                                             'department');

    // Security check - important to do this as early as possible to avoid
    // potential security holes or just too much wasted processing
    if (!xarSecurityCheck('ContactEdit')) return;


    // The user API function is called.  This takes the number of items
    // required and the first number in the list of all items, which we
    // obtained from the input and gets us the information on the appropriate
    // items.
    $list = xarModAPIFunc('contact',
                          'user',
                          'getall',
                          array('startnum' => $startnum,
                                'numitems' => xarModGetVar('contact',
                                                          'itemsperpage')));

    // Check for exceptions
    if (!isset($item) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; // throw back


    // Check individual permissions for Edit / Delete
    // Note : we could use a foreach ($items as $item) here as well, as
    // shown in xaruser.php, but as an contact, we'll adapt the $items array
    // 'in place', and *then* pass the complete items array to $data
    for ($i = 0; $i < count($list); $i++) {
        $item = $list[$i];
        if(xarSecurityCheck('ContactEdit',0,'Item',"$item[firstname]:All:$item[id]")){
            $list[$i]['editurl'] = xarModURL('contact',
                                             'admin',
                                             'modify_contact',
                                              array('id' => $item['id']));



    // Check for exceptions
    if (!isset($item) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; // throw back

        } else {
            $list[$i]['editurl'] = '';
        }
        // The user API function is called.  This takes the number of items
    // required and the first number in the list of all items, which we
    // obtained from the input and gets us the information on the appropriate
    // items.

        $list[$i]['edittitle'] = xarML('Edit');
        if(xarSecurityCheck('ContactDelete',0,'Item',"$item[firstname]:All:$item[id]")){
            $list[$i]['deleteurl'] = xarModURL('contact',
                                               'admin',
                                               'delete',
                                               array('id' => $item['id']));
        } else {
            $list[$i]['deleteurl'] = '';
        }
        $list[$i]['deletetitle'] = xarML('Delete');
    }

  $data['list'] = $list;

// TODO : add a pager (once it exists in BL)
    $data['pager'] = '';


    // Return the template variables defined in this function
    return $data;
}

?>
<?php

function contact_admin_add_departments()
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

    // Security check - important to do this as early as possible to avoid
    // potential security holes or just too much wasted processing
    if (!xarSecurityCheck('ContactEdit')) return;

    // Generate a one-time authorisation code for this operation
    $data['authid'] = xarSecGenAuthKey();

    // Specify some labels for display
    $data['departmentlabel'] = xarVarPrepForDisplay(xarML('Add New Department'));
    $data['department_idlabel'] = xarVarPrepForDisplay(xarML('DEPARTMENTID'));
    $data['adddepartmentbutton'] = xarVarPrepForDisplay(xarML('Commit'));

    list($id,
         $email,
         $name,
         $phone,
         $fax,
         $state,
         $country,
         $cid,
         $hide,
         $deletedepartment) = xarVarCleanFromInput('id',
                                        'email',
                                        'name',
                                        'phone',
                                        'fax',
                                        'state',
                                        'country',
                                        'cid',
                                        'hide',
                                        'deletedepartment');
    // The API function is called.  Note that the name of the API function and
    // the name of this function are identical, this helps a lot when
    // programming more complex modules.  The arguments to the function are
    // passed in as their own arguments array
    $items = xarModAPIFunc('contact',
                        'admin',
                        'getdepartment',
                        array('startnum' => $startnum,
                               'numitems' => xarModGetVar('contact',
                                                          'itemsperpage')));
     // Check for exceptions
    if (!isset($item) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; // throw back
    // The return value of the function is checked here, and if the function
    // suceeded then an appropriate message is posted.  Note that if the
    // function did not succeed then the API function should have already
    // posted a failure message so no action is required
      for ($i = 0; $i < count($items); $i++) {
        $item = $items[$i];

        $data['items'] = $items;
    }


    // Add the array of items to the template variables
    $data['items'] = $items;
    $item = array();
    $item['module'] = 'contact';
    $hooks = xarModCallHooks('city','new','',$item);
    if (empty($hooks)) {
        $data['hooks'] = '';
    } elseif (is_array($hooks)) {
        $data['hooks'] = join('',$hooks);
    } else {
        $data['hooks'] = $hooks;
    }

    // Return the template variables defined in this function

    return $data;
}

?>
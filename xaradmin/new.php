<?php

/**
 * add new item
 * This is a standard function that is called whenever an administrator
 * wishes to create a new module item
 */
function contact_admin_new()
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
    if (!xarSecurityCheck('ContactAdd')) return;

    // Generate a one-time authorisation code for this operation
    $data['authid'] = xarSecGenAuthKey();

    // Specify some labels for display
    $data['namelabel'] = xarVarPrepForDisplay(xarML('Company Name:'));
    $data['addresslabel'] = xarVarPrepForDisplay(xarML('Company Address:'));
    $data['address2label'] = xarVarPrepForDisplay(xarML('Company Address2:'));
    $data['citylabel'] = xarVarPrepForDisplay(xarML('Company City:'));
    $data['statelabel'] = xarVarPrepForDisplay(xarML('Company State/Province:'));
    $data['ziplabel'] = xarVarPrepForDisplay(xarML('Company Zipcode:'));
    $data['countrylabel'] = xarVarPrepForDisplay(xarML('Company Country:'));
    $data['phonelabel'] = xarVarPrepForDisplay(xarML('Company Phone:'));
    $data['faxlabel'] = xarVarPrepForDisplay(xarML('Company Fax:'));
    $data['emaillabel'] = xarVarPrepForDisplay(xarML('Company e-Mail:'));
    $data['logolabel'] = xarVarPrepForDisplay(xarML('Company Logo:'));
    $data['hidelabel'] = xarVarPrepForDisplay(xarML('Hide:'));
    $data['defaultcountrylabel'] = xarVarPrepForDisplay(xarML('Default Country:'));
    $data['addbutton'] = xarVarPrepForDisplay(xarML('ADD'));

    // Get parameters from whatever input we need.  All arguments to this
    // function should be obtained from xarVarCleanFromInput(), getting them
    // from other places such as the environment is not allowed, as that makes
    // assumptions that will not hold in future versions of Xaraya

    list($id,
         $name,
         $address,
         $address2,
         $city,
         $state,
         $zip,
         $country,
         $phone,
         $fax,
         $mail,
         $company_logo_upload,
         $lastlogo,
         $number,
         $companylogo) = xarVarCleanFromInput('id',
                                       'name',
                                        'address',
                                        'address2',
                                        'city',
                                        'state',
                                        'zip',
                                        'country',
                                        'phone',
                                        'fax',
                                        'mail',
                                        'company_logo_upload',
                                        'lastlogo',
                                        'number',
                                        'companylogo');
    // The API function is called.  Note that the name of the API function and
    // the name of this function are identical, this helps a lot when
    // programming more complex modules.  The arguments to the function are
    // passed in as their own arguments array
    $items = xarModAPIFunc('contact',
                        'admin',
                        'getcompany',
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
    }

    // Check for an error with the database code, adodb has already raised
    // the exception so we just return

     // Add the array of items to the template variables
    $compcity = $item['city'];
    $data['compcity'] = $item['city'];
    $data['items'] = $item;
    $data['name'] = $item['name'];
    $data['address'] = $item['address'];
    $data['address2'] = $item['address2'];
    $data['city'] = $item['city'];
    $data['state'] = $item['state'];
    $data['zip'] = $item['zip'];
    $data['country'] = $item['country'];
    $data['phone'] = $item['phone'];
    $data['fax'] = $item['fax'];
    $data['mail'] = $item['mail'];
    $data['companylogo'] = $item['companylogo'];

    // Get parameters from whatever input we need.  All arguments to this
    // function should be obtained from xarVarCleanFromInput(), getting them
    // from other places such as the environment is not allowed, as that makes
    // assumptions that will not hold in future versions of Xaraya

    list($cityname,
         $cid) = xarVarCleanFromInput('cityname',
                                        'cid');
    // The API function is called.  Note that the name of the API function and
    // the name of this function are identical, this helps a lot when
    // programming more complex modules.  The arguments to the function are
    // passed in as their own arguments array
    $city = xarModAPIFunc('contact',
                        'admin',
                        'getcity',
                        array('startnum' => $startnum,
                               'numitems' => xarModGetVar('contact',
                                                          'itemsperpage')));
     // Check for exceptions
    if (!isset($item) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; // throw back
    // The return value of the function is checked here, and if the function
    // suceeded then an appropriate message is posted.  Note that if the
    // function did not succeed then the API function should have already
    // posted a failure message so no action is required
      for ($i = 0; $i < count($city); $i++) {
        $item = $city[$i];
        $data['city'] = $city;
      }
     // Add the array of items to the template variables
    $data['items'] = $items;


    $item = array();
    $item['module'] = 'contact';
    $hooks = xarModCallHooks('item','new','',$item);
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
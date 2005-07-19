<?php

/**
 * the main user function
 * This function is the default function, and is called whenever the module is
 * initiated without defining arguments.  As such it can be used for a number
 * of things, but most commonly it either just shows the module menu and
 * returns or calls whatever the module designer feels should be the default
 * function (often this is the view() function)
 */
function contact_user_main()
{
    // Security check - important to do this as early as possible to avoid
    // potential security holes or just too much wasted processing.  For the
    // main function we want to check that the user has at least overview
    // privilege for some item within this component, or else they won't be
    // able to see anything and so we refuse access altogether.  The lowest
    // level of access for users depends on the particular module, but it is
    // generally either 'overview' or 'read'
    if (!xarSecurityCheck('ContactRead')) return;

    // If you want to go directly to some default function, instead of
    // having a separate main function, you can simply call it here, and
    // use the same template for user-main.xard as for user-view.xard
    // return example_user_view();

    // Initialise the $data variable that will hold the data to be used in
    // the blocklayout template, and get the common menu configuration - it
    // helps if all of the module pages have a standard menu at the top to
    // support easy navigation
    $data = xarModAPIFunc('contact','user','menu');

    // Specify some other variables used in the blocklayout template
    $data['welcome'] = xarML('Welcome to the Contact module...');
    $data['items'] = array();

// Get parameters from whatever input we need.  All arguments to this
    // function should be obtained from xarVarCleanFromInput(), getting them
    // from other places such as the environment is not allowed, as that makes
    // assumptions that will not hold in future versions of Xaraya
    $startnum = xarVarCleanFromInput('startnum');
    // Initialise the $data variable that will hold the data to be used in
    // the blocklayout template, and get the common menu configuration - it
    // helps if all of the module pages have a standard menu at the top to
    // support easy navigation


    // Security check - important to do this as early as possible to avoid
    // potential security holes or just too much wasted processing
      if (!xarSecurityCheck('ContactRead')) return;

    // Generate a one-time authorisation code for this operation
    $data['authid'] = xarSecGenAuthKey();

    // Specify some labels for display
    $data['addpersons'] = xarML('<a href="index.php?module=contact&amp;type=admin&amp;func=add_contact">Add Contact Persons</a>');
    $data['contactmenu'] = xarML('<a href="index.php?module=contact&amp;type=admin">Contact Menu</a>');
    $data['listpersons'] = xarML('<a href="index.php?module=contact&amp;type=admin&amp;func=list_contact">Edit Contact Persons</a>');
    $data['addcontact_label'] = xarVarPrepForDisplay(xarML('Add Contact'));
    $data['contact_idlabel'] = xarVarPrepForDisplay(xarML('CONTACTID'));
    $data['addcontactbutton'] = xarVarPrepForDisplay(xarML('Commit'));
    $data['generalinfo_label'] = xarVarPrepForDisplay(xarML('General Contact Information'));
    $data['newdeptarment_label'] = xarVarPrepForDisplay(xarML('Add New Department:'));
    $data['department_label'] = xarVarPrepForDisplay(xarML('Department:'));
    $data['info_label'] = xarVarPrepForDisplay(xarML('Info'));
    $data['newtitle_label'] = xarVarPrepForDisplay(xarML('Add New Title:'));
    $data['title_label'] = xarVarPrepForDisplay(xarML('Title:'));
    $data['firstname_label'] = xarVarPrepForDisplay(xarML('Firstname:'));
    $data['lastname_label'] = xarVarPrepForDisplay(xarML('Lastname:'));
    $data['address_label'] = xarVarPrepForDisplay(xarML('Address:'));
    $data['address2_label'] = xarVarPrepForDisplay(xarML('Address2:'));
    $data['postalcode_label'] = xarVarPrepForDisplay(xarML('Postalcode:'));
    $data['city_label'] = xarVarPrepForDisplay(xarML('City:'));
    $data['state_label'] = xarVarPrepForDisplay(xarML('State/Province:'));
    $data['addcity_label'] = xarVarPrepForDisplay(xarML('Add Another City:'));
    $data['country_label'] = xarVarPrepForDisplay(xarML('Country:'));
    $data['howtocontact_label'] = xarVarPrepForDisplay(xarML('Ways to contact this person'));
    $data['phone_label'] = xarVarPrepForDisplay(xarML('Phone:'));
    $data['type_label'] = xarVarPrepForDisplay(xarML('Type:'));
    $data['fax_label'] = xarVarPrepForDisplay(xarML('Fax:'));
    $data['mobile_label'] = xarVarPrepForDisplay(xarML('Mobile:'));
    $data['pager_label'] = xarVarPrepForDisplay(xarML('Pager:'));
    $data['email_label'] = xarVarPrepForDisplay(xarML('Email:'));
    $data['imcontact_label'] = xarVarPrepForDisplay(xarML('Instant Messenger Contact Information'));
    $data['icq_label'] = xarVarPrepForDisplay(xarML('ICQ:'));
    $data['aim_label'] = xarVarPrepForDisplay(xarML('AIM:'));
    $data['yim_label'] = xarVarPrepForDisplay(xarML('YIM:'));
    $data['msnm_label'] = xarVarPrepForDisplay(xarML('MSNM:'));
    $data['options_label'] = xarVarPrepForDisplay(xarML('Configuration Options:'));
    $data['image_label'] = xarVarPrepForDisplay(xarML('Image:'));
    $data['lang_label'] = xarVarPrepForDisplay(xarML('Language:'));
    $data['visable_label'] = xarVarPrepForDisplay(xarML('Display?'));
    $data['active_label'] = xarVarPrepForDisplay(xarML('Make Active'));
    $data['displayall_label'] = xarVarPrepForDisplay(xarML('Display all fields?'));
    $data['note_label'] = xarVarPrepForDisplay(xarML('*Checked fields will be displayed on this persons contact page.'));

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
                        'user',
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
    $data['companyname'] = $item['name'];
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



 // Return the template variables defined in this function
    return $data;
}

?>
<?php

function contact_admin_add_contact()
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
    $data['addpersons'] = xarML('<a href="index.php?module=contact&amp;type=admin&amp;func=add_contact">Add Contact Persons</a>');
    $data['contactmenu'] = xarML('<a href="index.php?module=contact&amp;type=admin">Contact Menu</a>');
    $data['listpersons'] = xarML('<a href="index.php?module=contact&amp;type=admin&amp;func=list_contact">Edit Contact Persons</a>');
    $data['addcontact_label'] = xarVarPrepForDisplay(xarML('Add Contact'));
    $data['contact_idlabel'] = xarVarPrepForDisplay(xarML('Contact ID'));
    $data['addcontactbutton'] = xarVarPrepForDisplay(xarML('Commit'));
    $data['generalinfo_label'] = xarVarPrepForDisplay(xarML('General Contact Information'));
    $data['newdeptarment_label'] = xarVarPrepForDisplay(xarML('Add New Department:'));
    $data['department_label'] = xarVarPrepForDisplay(xarML('Department:'));
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
         $firstname,
         $lastname,
         $address,
         $address2,
         $city,
         $state,
         $zip,
         $country,
         $mail,
         $phone,
         $fax,
         $mobile,
         $pager,
         $typephone,
         $typefax,
         $typemobile,
         $typepager,
         $active,
         $ICQ,
         $AIM,
         $YIM,
         $MSNM,
         $titleID,
         $image,
         $hide,
         $deleteperson,
         $newcity,
         $department) = xarVarCleanFromInput('id',
                                        'firstname',
                                        'lastname',
                                        'address',
                                        'address2',
                                        'city',
                                        'state',
                                        'zip',
                                        'country',
                                        'mail',
                                        'phone',
                                        'fax',
                                        'mobile',
                                        'pager',
                                        'typephone',
                                        'typefax',
                                        'typemobile',
                                        'typepager',
                                        'active',
                                        'ICQ',
                                        'AIM',
                                        'YIM',
                                        'MSNM',
                                        'titleID',
                                        'image',
                                        'hide',
                                        'deleteperson',
                                        'newcity',
                                        'department');
    // The API function is called.  Note that the name of the API function and
    // the name of this function are identical, this helps a lot when
    // programming more complex modules.  The arguments to the function are
    // passed in as their own arguments array
    $departments = xarModAPIFunc('contact',
                        'admin',
                        'get_departments',
                        array('startnum' => $startnum,
                               'numitems' => xarModGetVar('contact',
                                                          'itemsperpage')));
     // Check for exceptions
    if (!isset($item) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; // throw back
    // The return value of the function is checked here, and if the function
    // suceeded then an appropriate message is posted.  Note that if the
    // function did not succeed then the API function should have already
    // posted a failure message so no action is required
      for ($i = 0; $i < count($departments); $i++) {
        $item = $departments[$i];

         $data['departments'] = $departments;
    }
    // The API function is called.  Note that the name of the API function and
    // the name of this function are identical, this helps a lot when
    // programming more complex modules.  The arguments to the function are
    // passed in as their own arguments array
    $titles = xarModAPIFunc('contact',
                        'admin',
                        'get_titles',
                        array('startnum' => $startnum,
                               'numitems' => xarModGetVar('contact',
                                                          'itemsperpage')));
     // Check for exceptions
    if (!isset($titles) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; // throw back
    // The return value of the function is checked here, and if the function
    // suceeded then an appropriate message is posted.  Note that if the
    // function did not succeed then the API function should have already
    // posted a failure message so no action is required
      for ($i = 0; $i < count($titles); $i++) {
        $item = $titles[$i];

         $data['titles'] = $titles;
    }

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

    // The API function is called.  Note that the name of the API function and
    // the name of this function are identical, this helps a lot when
    // programming more complex modules.  The arguments to the function are
    // passed in as their own arguments array
    $country = xarModAPIFunc('contact',
                        'admin',
                        'get_country',
                        array('startnum' => $startnum,
                               'numitems' => xarModGetVar('contact',
                                                          'itemsperpage')));
     // Check for exceptions
    if (!isset($item) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; // throw back
    // The return value of the function is checked here, and if the function
    // suceeded then an appropriate message is posted.  Note that if the
    // function did not succeed then the API function should have already
    // posted a failure message so no action is required
      for ($i = 0; $i < count($country); $i++) {
        $item = $country[$i];

         $data['country'] = $country;
    }
    // The API function is called.  Note that the name of the API function and
    // the name of this function are identical, this helps a lot when
    // programming more complex modules.  The arguments to the function are
    // passed in as their own arguments array
    $items = xarModAPIFunc('contact',
                        'admin',
                        'getlocation',
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
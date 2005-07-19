<?php

/**
 * This is a standard function that is called with the results of the
 * form supplied by xarModFunc('contact','admin','new') to create a new item
 * @param 'name' the name of the item to be created
 * @param 'number' the number of the item to be created
 */
function contact_admin_createcompanyinfo($args)
{
   if (phpversion() >="4.2.0") extract($_POST);
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $companytable = $xartable['contact_company'];
    $query = "SELECT xar_id FROM $companytable";
    $result = $dbconn->Execute($query);
    // Check for an error with the database code, adodb has already raised
    // the exception so we just return
    if (!$result) return;
    for (; !$result->EOF; $result->MoveNext() ) {
           $company_id = $result->fields[0];
    }

    $data['company_id'] = $company_id;

     // Security check - important to do this as early as possible to avoid
    // potential security holes or just too much wasted processing
    if (!xarSecurityCheck('ContactEdit')) return;

    // Get parameters from whatever input we need.  All arguments to this
    // function should be obtained from xarVarCleanFromInput(), getting them
    // from other places such as the environment is not allowed, as that makes
    // assumptions that will not hold in future versions of Xaraya
    list($name,
         $address,
         $address2,
         $city,
         $state,
         $zipcode,
         $country,
         $phone,
         $fax,
         $email,
         $company_logo_upload,
         $AddNewCity,
         $number,
         $lastlogo,
         $companylogo) = xarVarCleanFromInput('name',
                                        'address',
                                        'address2',
                                        'city',
                                        'state',
                                        'zipcode',
                                        'country',
                                        'phone',
                                        'fax',
                                        'email',
                                        'company_logo_upload',
                                        'AddNewCity',
                                        'number',
                                        'lastlogo',
                                        'companylogo');

    // Admin functions of this type can be called by other modules.  If this
    // happens then the calling module will be able to pass in arguments to
    // this function through the $args parameter.  Hence we extract these
    // arguments *after* we have obtained any form-based input through
    // xarVarCleanFromInput().
    extract($args);

    if (is_uploaded_file($_FILES['company_logo_upload']['tmp_name'])) {
       $filename = ($_FILES['company_logo_upload']['name']);
       $companylogo = "modules/contact/xarimages/logo".substr($filename,-4,4);
       $ext=explode('.',$filename);
       $ext=strtolower($ext[count($ext)-1]);
            if (preg_match('/^(gif|png|jpe?g)$/',$ext)) {
                copy($_FILES['company_logo_upload']['tmp_name'], "modules/contact/xarimages/logo".substr($filename,-4,4));
            }else{
                 $msg = xarML('You can only upload file types of gif, png, jpeg or jpg.',
                              'module');
                               xarExceptionSet(XAR_USER_EXCEPTION,'error class decriptor',new DefaultUserException($msg));
                 return;
                 }
      }else{
       $companylogo = $lastlogo;
    }

    if($company_id > '0') {

       if (isset($AddNewCity)) {

          $CompCity = $city;
          $newCityName = $AddNewCity;

          if($newCityName){

            $CompCity = xarModAPIFunc('contact',
                        'admin',
                        'addcitycompany',
                        array('id' => $company_id,
                               'name' => $newCityName,
                               'number' => $number));

            // The return value of the function is checked here, and if the function
            // suceeded then an appropriate message is posted.  Note that if the
            // function did not succeed then the API function should have already
            // posted a failure message so no action is required
            if (!isset($CompCity) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; // throw back
            // Success
            xarSessionSetVar('statusmsg', xarML('CITY_CREATED'));

            // This function generated no output, and so now it is complete we redirect
            // the user to an appropriate page for them to carry on their work
            xarResponseRedirect(xarModURL('contact', 'admin', 'new'));
         }
       }

    // Confirm authorisation code.  This checks that the form had a valid
    // authorisation code attached to it.  If it did not then the function will
    // proceed no further as it is possible that this is an attempt at sending
    // in false data to the system
    if (!xarSecConfirmAuthKey()) return;

    // The API function is called.  Note that the name of the API function and
    // the name of this function are identical, this helps a lot when
    // programming more complex modules.  The arguments to the function are
    // passed in as their own arguments array
    $id = xarModAPIFunc('contact',
                        'admin',
                        'update',
                        array('name' => $name,
                               'address' => $address,
                               'address2' => $address2,
                               'city' => $city,
                               'state' => $state,
                               'zipcode' => $zipcode,
                               'country' => $country,
                               'phone' => $phone,
                               'fax' => $fax,
                               'email' => $email,
                               'companylogo' => $companylogo));

    // The return value of the function is checked here, and if the function
    // suceeded then an appropriate message is posted.  Note that if the
    // function did not succeed then the API function should have already
    // posted a failure message so no action is required
    if (!isset($id) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; // throw back

    // Success
    xarSessionSetVar('statusmsg', xarML('contactCREATED'));

    // This function generated no output, and so now it is complete we redirect
    // the user to an appropriate page for them to carry on their work
    xarResponseRedirect(xarModURL('contact', 'admin', 'new'));

    // Return
    return true;

}

// **************Added create here****************

  // Security check - important to do this as early as possible to avoid
    // potential security holes or just too much wasted processing
    if (!xarSecurityCheck('ContactEdit')) return;

    // Get parameters from whatever input we need.  All arguments to this
    // function should be obtained from xarVarCleanFromInput(), getting them
    // from other places such as the environment is not allowed, as that makes
    // assumptions that will not hold in future versions of Xaraya
    list($name,
         $address,
         $address2,
         $city,
         $state,
         $zipcode,
         $country,
         $phone,
         $fax,
         $email,
         $logo) = xarVarCleanFromInput('name',
                                        'address',
                                        'address2',
                                        'city',
                                        'state',
                                        'zipcode',
                                        'country',
                                        'phone',
                                        'fax',
                                        'email',
                                        'logo');

    // Admin functions of this type can be called by other modules.  If this
    // happens then the calling module will be able to pass in arguments to
    // this function through the $args parameter.  Hence we extract these
    // arguments *after* we have obtained any form-based input through
    // xarVarCleanFromInput().
    extract($args);

    // Confirm authorisation code.  This checks that the form had a valid
    // authorisation code attached to it.  If it did not then the function will
    // proceed no further as it is possible that this is an attempt at sending
    // in false data to the system
    if (!xarSecConfirmAuthKey()) return;


    // The API function is called.  Note that the name of the API function and
    // the name of this function are identical, this helps a lot when
    // programming more complex modules.  The arguments to the function are
    // passed in as their own arguments array
    $id = xarModAPIFunc('contact',
                        'admin',
                        'create',
                        array('name' => $name,
                               'address' => $address,
                               'address2' => $address2,
                               'city' => $city,
                               'state' => $state,
                               'zipcode' => $zipcode,
                               'country' => $country,
                               'phone' => $phone,
                               'fax' => $fax,
                               'email' => $email,
                              'logo' => $logo));

    // The return value of the function is checked here, and if the function
    // suceeded then an appropriate message is posted.  Note that if the
    // function did not succeed then the API function should have already
    // posted a failure message so no action is required
    if (!isset($id) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; // throw back

    // Success
    xarSessionSetVar('statusmsg', xarML('contactCREATED'));

    // This function generated no output, and so now it is complete we redirect
    // the user to an appropriate page for them to carry on their work
    xarResponseRedirect(xarModURL('contact', 'admin', 'new'));

    // Return
    return true;
}

?>

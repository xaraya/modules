<?php

/**
 * This is a standard function that is called with the results of the
 * form supplied by xarModFunc('contact','admin','modify') to update a current item
 * @param 'exid' the id of the item to be updated
 * @param 'name' the name of the item to be updated
 * @param 'number' the number of the item to be updated
 */
function contact_admin_update_contact($args)
{
    // Get parameters from whatever input we need.  All arguments to this
    // function should be obtained from xarVarCleanFromInput(), getting them
    // from other places such as the environment is not allowed, as that makes
    // assumptions that will not hold in future versions of Xaraya
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
         $department,
         $lastimage,
         $showname,
         $showaddress,
         $showaddress2,
         $showcity,
         $showstate,
         $showzip,
         $showcountry,
         $showemail,
         $showphone,
         $showfax,
         $showmobile,
         $showpager,
         $showICQ,
         $showAIM,
         $showYIM,
         $showMSNM,
         $showtitle,
         $showdepartment,
         $showimage) = xarVarCleanFromInput('id',
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
                                             'department',
                                             'lastimage',
                                             'showname',
                                             'showaddress',
                                             'showaddress2',
                                             'showcity',
                                             'showstate',
                                             'showzip',
                                             'showcountry',
                                             'showemail',
                                             'showphone',
                                             'showfax',
                                             'showmobile',
                                             'showpager',
                                             'showICQ',
                                             'showAIM',
                                             'showYIM',
                                             'showMSNM',
                                             'showtitle',
                                             'showdepartment',
                                             'showimage');

    // User functions of this type can be called by other modules.  If this
    // happens then the calling module will be able to pass in arguments to
    // this function through the $args parameter.  Hence we extract these
    // arguments *after* we have obtained any form-based input through
    // xarVarCleanFromInput().
    extract($args);

    // At this stage we check to see if we have been passed $objectid, the
    // generic item identifier.  This could have been passed in by a hook or
    // through some other function calling this as part of a larger module, but
    // if it exists it overrides $exid
    //
    // Note that this module couuld just use $objectid everywhere to avoid all
    // of this munging of variables, but then the resultant code is less
    // descriptive, especially where multiple objects are being used.  The
    // decision of which of these ways to go is up to the module developer
    if (!empty($objectid)) {
        $id = $objectid;
    }

     if (is_uploaded_file($_FILES['image']['tmp_name'])) {
       $filename = ($_FILES['image']['name']);
       $image = "modules/contact/xarimages/". $lastname.substr($filename,-4,4);
       $ext=explode('.',$filename);
       $ext=strtolower($ext[count($ext)-1]);
            if (preg_match('/^(gif|png|jpe?g)$/',$ext)) {
                copy($_FILES['image']['tmp_name'], "modules/contact/xarimages/" . $lastname.substr($filename,-4,4));
                $image = "modules/contact/xarimages/". $lastname.substr($filename,-4,4);
            }else{
                 $msg = xarML('You can only upload file types of gif, png, jpeg or jpg.',
                              'module');
                               xarExceptionSet(XAR_USER_EXCEPTION,'error class decriptor',new DefaultUserException($msg));
                 return;
                 }
      }else{
       $image = $lastimage;
    }
    // Confirm authorisation code.  This checks that the form had a valid
    // authorisation code attached to it.  If it did not then the function will
    // proceed no further as it is possible that this is an attempt at sending
    // in false data to the system
    if (!xarSecConfirmAuthKey()) return;

    // Notable by its absence there is no security check here.  This is because
    // the security check is carried out within the API function and as such we
    // do not duplicate the work here

    // The API function is called.  Note that the name of the API function and
    // the name of this function are identical, this helps a lot when
    // programming more complex modules.  The arguments to the function are
    // passed in as their own arguments array.
    //
    // The return value of the function is checked here, and if the function
    // suceeded then an appropriate message is posted.  Note that if the
    // function did not succeed then the API function should have already
    // posted a failure message so no action is required
    if(!xarModAPIFunc('contact',
                    'admin',
                    'update_contact',
                    array('id' => $id,
                          'firstname' => $firstname,
                          'lastname' => $lastname,
                          'address' => $address,
                          'address2' => $address2,
                          'city' => $city,
                          'state' => $state,
                          'zip' => $zip,
                          'country' => $country,
                          'mail' => $mail,
                          'phone' => $phone,
                          'fax' => $fax,
                          'mobile' => $mobile,
                          'pager' => $pager,
                          'typephone' => $typephone,
                          'typefax' => $typefax,
                          'typemobile' => $typemobile,
                          'typepager' => $typepager,
                          'active' => $active,
                          'ICQ' => $ICQ,
                          'AIM' => $AIM,
                          'YIM' => $YIM,
                          'MSNM' => $MSNM,
                          'titleID' => $titleID,
                          'image' => $image,
                          'hide' => $hide,
                          'department' => $department,
                          'showname' => $showname,
                          'showaddress' => $showaddress,
                          'showaddress2' => $showaddress2,
                          'showcity' => $showcity,
                          'showstate' => $showstate,
                          'showzip' => $showzip,
                          'showcountry' => $showcountry,
                          'showemail' => $showemail,
                          'showphone' =>$showphone,
                          'showfax' => $showfax,
                          'showmobile' => $showmobile,
                          'showpager' => $showpager,
                          'showICQ' => $showICQ,
                          'showAIM' => $showAIM,
                          'showYIM' => $showYIM,
                          'showMSNM' => $showMSNM,
                          'showtitle' => $showtitle,
                          'showdepartment' => $showdepartment,
                          'showimage' => $showimage))) {

    }

    xarSessionSetVar('statusmsg', xarML('CONTACTUPDATED'));

    // This function generated no output, and so now it is complete we redirect
    // the user to an appropriate page for them to carry on their work
    xarResponseRedirect(xarModURL('contact', 'admin', 'list_contact'));

    // Return
    return true;
}

?>

<?php

function contact_admin_create_contact($args)
{

    // Get parameters from whatever input we need.  All arguments to this
    // function should be obtained from xarVarCleanFromInput(), getting them
    // from other places such as the environment is not allowed, as that makes
    // assumptions that will not hold in future versions of Xaraya
    list($id,
         $department,
         $title,
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
         $udfirstname,
         $udlastname,
         $udaddress,
         $udaddress2,
         $udcity,
         $udstate,
         $udzip,
         $udcountry,
         $udmail,
         $udphone,
         $udfax,
         $udmobile,
         $udpager,
         $udtypephone,
         $udtypefax,
         $udtypemobile,
         $udtypepager,
         $udactive,
         $udICQ,
         $udAIM,
         $udYIM,
         $udMSNM,
         $udtitleID,
         $udimage,
         $udhide,
         $addnewcontact,
         $adddepartment,
         $newtitles,
         $newcity,
         $lastimage) = xarVarCleanFromInput('id',
                                         'department',
                                         'title',
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
                                         'addfirstname',
                                         'addlastname',
                                         'addaddress',
                                         'addaddress2',
                                         'addcity',
                                         'addstate',
                                         'addzip',
                                         'addcountry',
                                         'addmail',
                                         'addphone',
                                         'addfax',
                                         'addmobile',
                                         'addpager',
                                         'addtypephone',
                                         'addtypefax',
                                         'addtypemobile',
                                         'addtypepager',
                                         'addactive',
                                         'addICQ',
                                         'addAIM',
                                         'addYIM',
                                         'addMSNM',
                                         'addtitleID',
                                         'addimage',
                                         'addhide',
                                         'addnewcontact',
                                         'adddepartment',
                                         'newtitles',
                                         'newcity',
                                         'lastimage');

    // Admin functions of this type can be called by other modules.  If this
    // happens then the calling module will be able to pass in arguments to
    // this function through the $args parameter.  Hence we extract these
    // arguments *after* we have obtained any form-based input through
    // xarVarCleanFromInput().

    // Confirm authorisation code.  This checks that the form had a valid
    // authorisation code attached to it.  If it did not then the function will
    // proceed no further as it is possible that this is an attempt at sending
    // in false data to the system
    if (!xarSecConfirmAuthKey()) return;

    if ($newcity != "") {


            $CompCity = xarModAPIFunc('contact',
                        'admin',
                        'create_city',
                        array('newcity' => $newcity));

            // The return value of the function is checked here, and if the function
            // suceeded then an appropriate message is posted.  Note that if the
            // function did not succeed then the API function should have already
            // posted a failure message so no action is required
            if (!isset($CompCity) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; // throw back
            // Success
            xarSessionSetVar('statusmsg', xarML('CITY_CREATED'));

            // This function generated no output, and so now it is complete we redirect
            // the user to an appropriate page for them to carry on their work
            xarResponseRedirect(xarModURL('contact', 'admin', 'add_contact'));

       }
         if ($newtitles != "") {

    // Notable by its absence there is no security check here.  This is because
    // the security check is carried out within the API function and as such we
    // do not duplicate the work here

    // The API function is called.  Note that the name of the API function and
    // the name of this function are identical, this helps a lot when
    // programming more complex modules.  The arguments to the function are
    // passed in as their own arguments array
    $coid = xarModAPIFunc('contact',
                        'admin',
                        'create_titles',
                        array('id' => $id,
                               'newtitles' => $newtitles,
                              'cid' => $cid));

    // The return value of the function is checked here, and if the function
    // suceeded then an appropriate message is posted.  Note that if the
    // function did not succeed then the API function should have already
    // posted a failure message so no action is required
    if (!isset($coid) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; // throw back

    // Success
    xarSessionSetVar('statusmsg', xarML('TITLESCREATED'));
    }

    if ($firstname != "") {

      if (is_uploaded_file($_FILES['image']['tmp_name'])) {
       $filename = ($_FILES['image']['name']);
       $companylogo = "modules/contact/xarimages/". $lastname.substr($filename,-4,4);
       $ext=explode('.',$filename);
       $ext=strtolower($ext[count($ext)-1]);
            if (preg_match('/^(gif|png|jpe?g)$/',$ext)) {
                copy($_FILES['image']['tmp_name'], "modules/contact/xarimages/" . $lastname.substr($filename,-4,4));
                $image = $lastname.substr($filename,-4,4);
            }else{
                 $msg = xarML('You can only upload file types of gif, png, jpeg or jpg.',
                              'module');
                               xarExceptionSet(XAR_USER_EXCEPTION,'error class decriptor',new DefaultUserException($msg));
                 return;
                 }
      }else{
       $image = $lastimage;
    }
    // Notable by its absence there is no security check here.  This is because
    // the security check is carried out within the API function and as such we
    // do not duplicate the work here

    // The API function is called.  Note that the name of the API function and
    // the name of this function are identical, this helps a lot when
    // programming more complex modules.  The arguments to the function are
    // passed in as their own arguments array
    $coid = xarModAPIFunc('contact',
                        'admin',
                        'create_contact',
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
                              'phone' => $phone,
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
                              'addnewcontact' => $addnewcontact));

    // The return value of the function is checked here, and if the function
    // suceeded then an appropriate message is posted.  Note that if the
    // function did not succeed then the API function should have already
    // posted a failure message so no action is required
    if (!isset($coid) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; // throw back

    // Success
    xarSessionSetVar('statusmsg', xarML('CONTACTCREATED'));

    $contacttype = xarVarPrepForStore("P");
    list($showname,
         $showaddress,
         $showaddress2,
         $showcity,
         $showstate,
         $showpostalcode,
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
         $showimage) = xarVarCleanFromInput('showname',
                                         'showaddress',
                                         'showaddress2',
                                         'showcity',
                                         'showstate',
                                         'showpostalcode',
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



    // The API function is called.  Note that the name of the API function and
    // the name of this function are identical, this helps a lot when
    // programming more complex modules.  The arguments to the function are
    // passed in as their own arguments array
    $coaid = xarModAPIFunc('contact',
                        'admin',
                        'create_contact_attributes',
                        array('id' => $coid,
                              'contacttype' => $contacttype,
                              'showname' => $showname,
                              'showaddress' => $showaddress,
                              'showaddress2' => $showaddress2,
                              'showcity' => $showcity,
                              'showstate' => $showstate,
                              'showpostalcode' => $showpostalcode,
                              'showcountry' => $showcountry,
                              'showemail' => $showemail,
                              'showphone' => $showphone,
                              'showfax' => $showfax,
                              'showphone' => $showphone,
                              'showmobile' => $showmobile,
                              'showpager' => $showpager,
                              'showICQ' => $showICQ,
                              'showAIM' => $showAIM,
                              'showYIM' => $showYIM,
                              'showMSNM' => $showMSNM,
                              'showtitle' => $showtitle,
                              'showdepartmet' => $showdepartment,
                              'showimage' => $showimage));

    // The return value of the function is checked here, and if the function
    // suceeded then an appropriate message is posted.  Note that if the
    // function did not succeed then the API function should have already
    // posted a failure message so no action is required
    if (!isset($coaid) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; // throw back

    // Success
    xarSessionSetVar('statusmsg', xarML('CONTACTATTRIBUTESCREATED'));

    list ($id,
          $department) = xarVarCleanFromInput('id',
                                               'department');

    // The API function is called.  Note that the name of the API function and
    // the name of this function are identical, this helps a lot when
    // programming more complex modules.  The arguments to the function are
    // passed in as their own arguments array
    $codid = xarModAPIFunc('contact',
                        'admin',
                        'create_contact_departments',
                        array('id' => $coid,
                              'department' => $department));

    // The return value of the function is checked here, and if the function
    // suceeded then an appropriate message is posted.  Note that if the
    // function did not succeed then the API function should have already
    // posted a failure message so no action is required
    if (!isset($codid) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; // throw back

    // Success
    xarSessionSetVar('statusmsg', xarML('CONTACTDEPARTMENTIDCREATED'));

    // This function generated no output, and so now it is complete we redirect
    // the user to an appropriate page for them to carry on their work
    xarResponseRedirect(xarModURL('contact', 'admin', 'add_contact'));

   }

     if(isset($cid)) {

    // The API function is called.  Note that the name of the API function and
    // the name of this function are identical, this helps a lot when
    // programming more complex modules.  The arguments to the function are
    // passed in as their own arguments array
    $coid = xarModAPIFunc('contact',
                        'admin',
                        'delete_contact',
                        array('id' => $id));

    // The return value of the function is checked here, and if the function
    // suceeded then an appropriate message is posted.  Note that if the
    // function did not succeed then the API function should have already
    // posted a failure message so no action is required
    if (!isset($coid) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; // throw back

    // Success
    xarSessionSetVar('statusmsg', xarML('DEPARTMENTDELETED'));

    // This function generated no output, and so now it is complete we redirect
    // the user to an appropriate page for them to carry on their work
    xarResponseRedirect(xarModURL('contact', 'admin', 'add_contact'));

    // Return
    return true;
    }

}
?>
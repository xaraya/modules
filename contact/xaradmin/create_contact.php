<?php

function contact_admin_create_contact($args)
{

    // Get parameters from whatever input we need.  All arguments to this
    // function should be obtained from xarVarCleanFromInput(), getting them
    // from other places such as the environment is not allowed, as that makes
    // assumptions that will not hold in future versions of Xaraya
     if (!xarVarFetch('id', 'isset', $id, '', XARVAR_NOT_REQUIRED)) return;
     if (!xarVarFetch('department', 'str:1:', $department, '', XARVAR_NOT_REQUIRED)) return;
     if (!xarVarFetch('title', 'str:1:', $title, '', XARVAR_NOT_REQUIRED)) return;
     if (!xarVarFetch('firstname', 'str:1:', $firstname, '', XARVAR_NOT_REQUIRED)) return;
     if (!xarVarFetch('lastname', 'str:1:', $lastname, '', XARVAR_NOT_REQUIRED)) return;
     if (!xarVarFetch('address', 'str:1:', $address, '', XARVAR_NOT_REQUIRED)) return;
     if (!xarVarFetch('address2', 'str:1:', $address2, '', XARVAR_NOT_REQUIRED)) return;
     if (!xarVarFetch('city', 'str:1:', $city, '', XARVAR_NOT_REQUIRED)) return;
     if (!xarVarFetch('state', 'str:1:', $state, '', XARVAR_NOT_REQUIRED)) return;
     if (!xarVarFetch('zip', 'str:1:', $zip, '', XARVAR_NOT_REQUIRED)) return;
     if (!xarVarFetch('country', 'str:1:', $country, '', XARVAR_NOT_REQUIRED)) return;
     if (!xarVarFetch('mail', 'str:1:', $mail, '', XARVAR_NOT_REQUIRED)) return;
     if (!xarVarFetch('phone', 'str:1:', $phone, '', XARVAR_NOT_REQUIRED)) return;
     if (!xarVarFetch('fax', 'str:1:', $fax, '', XARVAR_NOT_REQUIRED)) return;
     if (!xarVarFetch('mobile', 'str:1:', $mobile, '', XARVAR_NOT_REQUIRED)) return;
     if (!xarVarFetch('pager', 'str:1:', $pager, '', XARVAR_NOT_REQUIRED)) return;
     if (!xarVarFetch('typephone', 'str:1:', $typephone, '', XARVAR_NOT_REQUIRED)) return;
     if (!xarVarFetch('typefax', 'str:1:', $typefax, '', XARVAR_NOT_REQUIRED)) return;
     if (!xarVarFetch('typemobile', 'str:1:', $typemobile, '', XARVAR_NOT_REQUIRED)) return;
     if (!xarVarFetch('typepager', 'str:1:', $typepager, '', XARVAR_NOT_REQUIRED)) return;
     if (!xarVarFetch('active', 'isset', $active, '', XARVAR_NOT_REQUIRED)) return;
     if (!xarVarFetch('ICQ', 'str:1:', $ICQ, '', XARVAR_NOT_REQUIRED)) return;
     if (!xarVarFetch('AIM', 'str:1:', $AIM, '', XARVAR_NOT_REQUIRED)) return;
     if (!xarVarFetch('YIM', 'str:1:', $YIM, '', XARVAR_NOT_REQUIRED)) return;
     if (!xarVarFetch('MSNM', 'str:1:', $MSNM, '', XARVAR_NOT_REQUIRED)) return;
     if (!xarVarFetch('titleID', 'str:1:', $titleID, '', XARVAR_NOT_REQUIRED)) return;
     if (!xarVarFetch('image', 'str:1:', $image, '', XARVAR_NOT_REQUIRED)) return;
     if (!xarVarFetch('hide', 'str:1:', $hide, '', XARVAR_NOT_REQUIRED)) return;
     if (!xarVarFetch('udfirstname', 'str:1:', $addfirstname, '', XARVAR_NOT_REQUIRED)) return;
     if (!xarVarFetch('udlastname', 'str:1:', $addlastname, '', XARVAR_NOT_REQUIRED)) return;
     if (!xarVarFetch('udaddress', 'str:1:', $addaddress, '', XARVAR_NOT_REQUIRED)) return;
     if (!xarVarFetch('udaddress2', 'str:1:', $addaddress2, '', XARVAR_NOT_REQUIRED)) return;
     if (!xarVarFetch('udcity', 'str:1:', $addcity, '', XARVAR_NOT_REQUIRED)) return;
     if (!xarVarFetch('udstate', 'str:1:', $addstate, '', XARVAR_NOT_REQUIRED)) return;
     if (!xarVarFetch('udzip', 'str:1:', $addzip, '', XARVAR_NOT_REQUIRED)) return;
     if (!xarVarFetch('udcountry', 'str:1:', $addcountry, '', XARVAR_NOT_REQUIRED)) return;
     if (!xarVarFetch('udmail', 'str:1:', $addmail, '', XARVAR_NOT_REQUIRED)) return;
     if (!xarVarFetch('udphone', 'str:1:', $addphone, '', XARVAR_NOT_REQUIRED)) return;
     if (!xarVarFetch('udfax', 'str:1:', $addfax, '', XARVAR_NOT_REQUIRED)) return;
     if (!xarVarFetch('udmobile', 'str:1:', $addmobile, '', XARVAR_NOT_REQUIRED)) return;
     if (!xarVarFetch('udpager', 'str:1:', $addpager, '', XARVAR_NOT_REQUIRED)) return;
     if (!xarVarFetch('udtypephone', 'str:1:', $addtypephone, '', XARVAR_NOT_REQUIRED)) return;
     if (!xarVarFetch('udtypefax', 'str:1:', $addtypefax, '', XARVAR_NOT_REQUIRED)) return;
     if (!xarVarFetch('udtypemobile', 'str:1:', $addtypemobile, '', XARVAR_NOT_REQUIRED)) return;
     if (!xarVarFetch('udtypepager', 'str:1:', $addtypepager, '', XARVAR_NOT_REQUIRED)) return;
     if (!xarVarFetch('udactive', 'str:1:', $addactive, '', XARVAR_NOT_REQUIRED)) return;
     if (!xarVarFetch('udICQ', 'str:1:', $addICQ, '', XARVAR_NOT_REQUIRED)) return;
     if (!xarVarFetch('udAIM', 'str:1:', $addAIM, '', XARVAR_NOT_REQUIRED)) return;
     if (!xarVarFetch('udYIM', 'str:1:', $addYIM, '', XARVAR_NOT_REQUIRED)) return;
     if (!xarVarFetch('udMSNM', 'str:1:', $addMSNM, '', XARVAR_NOT_REQUIRED)) return;
     if (!xarVarFetch('udtitleID', 'str:1:', $addtitleID, '', XARVAR_NOT_REQUIRED)) return;
     if (!xarVarFetch('udimage', 'str:1:', $addimage, '', XARVAR_NOT_REQUIRED)) return;
     if (!xarVarFetch('udhide', 'isset', $addhide, '', XARVAR_NOT_REQUIRED)) return;
     if (!xarVarFetch('addnewcontact', 'isset', $addnewcontact, '', XARVAR_NOT_REQUIRED)) return;
     if (!xarVarFetch('adddepartment', 'isset', $adddepartment, '', XARVAR_NOT_REQUIRED)) return;
     if (!xarVarFetch('newtitles', 'isset', $newtitles, '', XARVAR_NOT_REQUIRED)) return;
     if (!xarVarFetch('newcity', 'isset', $newcity, '', XARVAR_NOT_REQUIRED)) return;
     if (!xarVarFetch('lastimage', 'isset', $lastimage, '', XARVAR_NOT_REQUIRED)) return;

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
                               xarErrorSet(XAR_USER_EXCEPTION,'error class decriptor',new DefaultUserException($msg));
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

    $contacttype = ("P");
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

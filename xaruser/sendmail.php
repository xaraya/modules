<?php

function contact_user_sendmail()
{
    // Security check - important to do this as early as possible to avoid
    // potential security holes or just too much wasted processing.  For the
    // main function we want to check that the user has at least overview
    // privilege for some item within this component, or else they won't be
    // able to see anything and so we refuse access altogether.  The lowest
    // level of access for users depends on the particular module, but it is
    // generally either 'overview' or 'read'
       if (!xarSecurityCheck('ContactOverview')) return;

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
     $edit = xarModAPIFunc('contact',
                         'user',
                         'get',
                         array('id' => $id));
    // Check for exceptions
    if (!isset($edit) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; // throw back

     $data = xarModAPIFunc('contact','user','menu');
    $data['edit'] = $edit;
    $data['contact_id'] = $edit['id'];
    $data['depname'] = $edit['depname'];
    $data['titleName'] = $edit['titleName'];
    $data['TypePhone'] = $edit['TypePhone'];
    $data['TypeFax'] = $edit['TypeFax'];
    $data['TypeMobile'] = $edit['TypeMobile'];
    $data['TypePager'] = $edit['TypePager'];
    $data['editcity'] = $edit['city'];
    $data['firstname'] = $edit['firstname'];
    $data['lastname'] = $edit['lastname'];
    $data['address'] = $edit['address'];
    $data['address2'] = $edit['address2'];
    $data['city'] = $edit['city'];
    $data['state'] = $edit['state'];
    $data['zip'] = $edit['zip'];
    $data['editcountry'] = $edit['country'];
    $data['info'] = $edit['mail'];
    $data['phone'] = $edit['phone'];
    $data['fax'] = $edit['fax'];
    $data['mobile'] = $edit['mobile'];
    $data['pager'] = $edit['pager'];
    $data['typephone'] = $edit['typephone'];
    $data['typefax'] = $edit['typefax'];
    $data['typemobile'] = $edit['typemobile'];
    $data['typepager'] = $edit['typepager'];
    $data['active'] = $edit['active'];
    $data['ICQ'] = $edit['ICQ'];
    $data['AIM'] = $edit['AIM'];
    $data['YIM'] = $edit['YIM'];
    $data['MSNM'] = $edit['MSNM'];
    $data['titleID'] = $edit['titleID'];
    $data['titleName'] = $edit['titleName'];
    $data['image'] = $edit['image'];
    $data['hide'] = $edit['hide'];
    $data['showdepartment'] = $edit['showdepartment'];
    $data['showname'] = $edit['showname'];
    $data['showaddress'] = $edit['showaddress'];
    $data['showaddress2'] = $edit['showaddress2'];
    $data['showcity'] = $edit['showcity'];
    $data['showstate'] = $edit['showstate'];
    $data['showzip'] = $edit['showzip'];
    $data['showcountry'] = $edit['showcountry'];
    $data['showemail'] = $edit['showemail'];
    $data['showphone'] = $edit['showphone'];
    $data['showfax'] = $edit['showfax'];
    $data['showmobile'] = $edit['showmobile'];
    $data['showpager'] = $edit['showpager'];
    $data['showICQ'] = $edit['showICQ'];
    $data['showAIM'] = $edit['showAIM'];
    $data['showYIM'] = $edit['showYIM'];
    $data['showMSNM'] = $edit['showMSNM'];
    $data['showtitle'] = $edit['showtitle'];
    $data['showimage'] = $edit['showimage'];

    $data['yourname'] = xarVarPrepForDisplay(xarML('Your Name:'));
    $data['recepient'] = $edit['firstname'];
    $data['sender_name'] = xarVarPrepForDisplay(xarML('Senders Name:'));
    $data['yourphone'] = xarVarPrepForDisplay(xarML('Your Phone Number:'));
    $data['sender_phone'] = xarVarPrepForDisplay(xarML('Senders Phone:'));
    $data['youremail'] = xarVarPrepForDisplay(xarML('Your Email Address:'));
    $data['femail'] = xarVarPrepForDisplay(xarML('Your Email:'));
    $data['yourmessage'] = xarVarPrepForDisplay(xarML('Your Message:'));
    $data['send_me_copy'] = xarVarPrepForDisplay(xarML('Send me a copy of my mail'));
    $data['sender_name'] = xarVarPrepForDisplay(xarML('Senders Name:'));
    $data['sender_wantcopy'] = xarVarPrepForDisplay(xarML('Sender wants copy:'));
    $data['contactme'] = xarVarPrepForDisplay(xarML('Please Contact me:'));
    $data['sender_wants_contact'] = 1;
    $data['preview'] = xarVarPrepForDisplay(xarML('Preview'));
    $data['send'] = xarVarPrepForDisplay(xarML('Send'));
    $data['reset'] = xarVarPrepForDisplay(xarML('Reset'));
    $data['submit'] = xarVarPrepForDisplay(xarML('Submit'));
    // Specify some other variables used in the blocklayout template
    //$data['welcome'] = xarML('Welcome to this Contact module...');

    // Return the template variables defined in this function
    return $data;

    // Note : instead of using the $data variable, you could also specify
    // the different template variables directly in your return statement :
    //
    // return array('menutitle' => ...,
    //              'welcome' => ...,
    //              ... => ...);
}

?>
<?php

function contact_admin_create_location($args)
{

    // Get parameters from whatever input we need.  All arguments to this
    // function should be obtained from xarVarCleanFromInput(), getting them
    // from other places such as the environment is not allowed, as that makes
    // assumptions that will not hold in future versions of Xaraya
    list($id,
         $locationname,
         $cid,
         $newlocation) = xarVarCleanFromInput('id',
                                         'locationname',
                                         'cid',
                                         'newlocation');

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

   if ($newlocation != "") {

    // Notable by its absence there is no security check here.  This is because
    // the security check is carried out within the API function and as such we
    // do not duplicate the work here

    // The API function is called.  Note that the name of the API function and
    // the name of this function are identical, this helps a lot when
    // programming more complex modules.  The arguments to the function are
    // passed in as their own arguments array
    $coid = xarModAPIFunc('contact',
                        'admin',
                        'create_location',
                        array('id' => $id,
                               'newlocation' => $newlocation,
                              'cid' => $cid));

    // The return value of the function is checked here, and if the function
    // suceeded then an appropriate message is posted.  Note that if the
    // function did not succeed then the API function should have already
    // posted a failure message so no action is required
    if (!isset($coid) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; // throw back

    // Success
    xarSessionSetVar('statusmsg', xarML('CITYCREATED'));
    }

    if(isset($cid)) {

    // The API function is called.  Note that the name of the API function and
    // the name of this function are identical, this helps a lot when
    // programming more complex modules.  The arguments to the function are
    // passed in as their own arguments array
    $coid = xarModAPIFunc('contact',
                        'admin',
                        'delete_location',
                        array('id' => $id,
                              'cid' => $cid));

    // The return value of the function is checked here, and if the function
    // suceeded then an appropriate message is posted.  Note that if the
    // function did not succeed then the API function should have already
    // posted a failure message so no action is required
    if (!isset($coid) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; // throw back

    // Success
    xarSessionSetVar('statusmsg', xarML('CITYDELETED'));

    // This function generated no output, and so now it is complete we redirect
    // the user to an appropriate page for them to carry on their work
    xarResponseRedirect(xarModURL('contact', 'admin', 'add_location'));

    // Return
    return true;
    }

    // The API function is called.  Note that the name of the API function and
    // the name of this function are identical, this helps a lot when
    // programming more complex modules.  The arguments to the function are
    // passed in as their own arguments array
    $coid = xarModAPIFunc('contact',
                        'admin',
                        'update_location',
                        array('id' => $id,
                              'name' => $locationname,
                              'cid' => $cid));

    // The return value of the function is checked here, and if the function
    // suceeded then an appropriate message is posted.  Note that if the
    // function did not succeed then the API function should have already
    // posted a failure message so no action is required
    if (!isset($coid) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; // throw back

    // Success
    xarSessionSetVar('statusmsg', xarML('LOCATIONUPDATED'));

    // This function generated no output, and so now it is complete we redirect
    // the user to an appropriate page for them to carry on their work
    xarResponseRedirect(xarModURL('contact', 'admin', 'add_location'));

    // Return
    return true;
}

?>
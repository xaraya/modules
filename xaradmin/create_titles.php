<?php

function contact_admin_create_titles($args)
{

    // Get parameters from whatever input we need.  All arguments to this
    // function should be obtained from xarVarCleanFromInput(), getting them
    // from other places such as the environment is not allowed, as that makes
    // assumptions that will not hold in future versions of Xaraya
    list($id,
         $titlesname,
         $cid,
         $newtitles) = xarVarCleanFromInput('id',
                                         'titlesname',
                                         'cid',
                                         'newtitles');

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

         if(isset($cid)) {

    // The API function is called.  Note that the name of the API function and
    // the name of this function are identical, this helps a lot when
    // programming more complex modules.  The arguments to the function are
    // passed in as their own arguments array
    $coid = xarModAPIFunc('contact',
                        'admin',
                        'delete_titles',
                        array('id' => $id,
                              'cid' => $cid));

    // The return value of the function is checked here, and if the function
    // suceeded then an appropriate message is posted.  Note that if the
    // function did not succeed then the API function should have already
    // posted a failure message so no action is required
    if (!isset($coid) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; // throw back

    // Success
    xarSessionSetVar('statusmsg', xarML('TITLEDELETED'));

    // This function generated no output, and so now it is complete we redirect
    // the user to an appropriate page for them to carry on their work
    xarResponseRedirect(xarModURL('contact', 'admin', 'add_titles'));

    // Return
    return true;
   }
    // The API function is called.  Note that the name of the API function and
    // the name of this function are identical, this helps a lot when
    // programming more complex modules.  The arguments to the function are
    // passed in as their own arguments array
    $coid = xarModAPIFunc('contact',
                        'admin',
                        'update_titles',
                        array('id' => $id,
                              'name' => $titlesname,
                              'cid' => $cid));

    // The return value of the function is checked here, and if the function
    // suceeded then an appropriate message is posted.  Note that if the
    // function did not succeed then the API function should have already
    // posted a failure message so no action is required
    if (!isset($coid) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; // throw back

    // Success
    xarSessionSetVar('statusmsg', xarML('TITLESUPDATED'));

    // This function generated no output, and so now it is complete we redirect
    // the user to an appropriate page for them to carry on their work
    xarResponseRedirect(xarModURL('contact', 'admin', 'add_titles'));

    // Return
    return true;
}

?>
<?php

/**
 * This is a standard function to update the configuration parameters of the
 * module given the information passed back by the modification form
 */
function contact_admin_updateconfig()
{
    // Get parameters from whatever input we need.  All arguments to this
    // function should be obtained from xarVarCleanFromInput(), getting them
    // from other places such as the environment is not allowed, as that makes
    // assumptions that will not hold in future versions of Xaraya
    list($bold,
         $itemsperpage,
         $shorturls) = xarVarCleanFromInput('bold',
                                           'itemsperpage',
                                           'shorturls');

    // Confirm authorisation code.  This checks that the form had a valid
    // authorisation code attached to it.  If it did not then the function will
    // proceed no further as it is possible that this is an attempt at sending
    // in false data to the system
    if (!xarSecConfirmAuthKey()) return;

    // Update module variables.  Note that depending on the HTML structure used
    // to obtain the information from the user it is possible that the values
    // might be unset, so it is important to check them all and assign them
    // default values if required
    if (!isset($bold)) {
        $bold = 0;
    }
    xarModSetVar('contact', 'bold', $bold);

    if (!isset($itemsperpage) || !is_numeric($itemsperpage)) {
        $itemsperpage = 10;
    }
    xarModSetVar('contact', 'itemsperpage', $itemsperpage);

    if (!isset($shorturls)) {
        $shorturls = 0;
    }
    xarModSetVar('contact', 'SupportShortURLs', $shorturls);

    xarModCallHooks('module','updateconfig','contact',
                   array('module' => 'contact'));

    // This function generated no output, and so now it is complete we redirect
    // the user to an appropriate page for them to carry on their work
    xarResponseRedirect(xarModURL('contact', 'admin', 'modifyconfig'));

    // Return
    return true;
}

?>
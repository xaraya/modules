<?php

/**
 * This is a standard function to update the configuration parameters of the
 * module given the information passed back by the modification form
 */
function xarlinkme_admin_updateconfig()
{
    if (!xarVarFetch('itemsperpage', 'str:1:', $itemsperpage,  XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('imagedir', 'str:1:', $imagedir)) return;
    if (!xarVarFetch('pagetitle', 'str:1:', $pagetitle)) return;
    if (!xarVarFetch('instructions', 'str:1:', $instructions)) return;
    if (!xarVarFetch('instructions2', 'str:1:', $instructions2)) return;
    if (!xarVarFetch('txtintro', 'str:1:', $txtintro)) return;
    if (!xarVarFetch('txtadlead', 'str:1:', $txtadlead)) return;
    // Confirm authorisation code.  This checks that the form had a valid
    // authorisation code attached to it.  If it did not then the function will
    // proceed no further as it is possible that this is an attempt at sending
    // in false data to the system
    if (!xarSecConfirmAuthKey()) return;
    // Update module variables.  Note that the default values are set in
    // xarVarFetch when recieving the incoming values, so no extra processing
    // is needed when setting the variables here.
    xarModSetVar('xarlinkme', 'itemsperpage', $itemsperpage);
    xarModSetVar('xarlinkme', 'imagedir', $imagedir);
    xarModSetVar('xarlinkme', 'pagetitle', $pagetitle);
    xarModSetVar('xarlinkme', 'instructions', $instructions);
    xarModSetVar('xarlinkme', 'instructions2', $instructions2);
    xarModSetVar('xarlinkme', 'txtintro', $txtintro);
    xarModSetVar('xarlinkme', 'txtadlead',$txtadlead);

    xarModCallHooks('module','updateconfig','xarlinkme',
                   array('module' => 'xarlinkme'));

    // This function generated no output, and so now it is complete we redirect
    // the user to an appropriate page for them to carry on their work
    xarResponseRedirect(xarModURL('xarlinkme', 'admin', 'modifyconfig'));

    // Return
    return true;
}

?>

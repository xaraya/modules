<?php
/**
 * Overview Menu
 */
function trackback_admin_new()
{
    // Security Check
    if(!xarSecurityCheck('Addtrackback')) return;
    // Generate a one-time authorisation code for this operation
    $data['authid'] = xarSecGenAuthKey();
    // Specify some labels for display
    $data['submitlabel'] = xarVarPrepForDisplay(xarML('Submit'));
    // Return the template variables defined in this function
    return $data;
}
?>
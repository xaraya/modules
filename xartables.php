<?php
/**
* This function is called internally by the core whenever the module is
* loaded.  It adds in the information
*/
function mailbag_xartables()
{
    // Initialise table array
    $xartable = array();

    // Set the column names.  Note that the array has been formatted
    // on-screen to be very easy to read by a user.
    // Name for dq_helpdesk database entities
    $coprefix = xarConfigGetVar('prefix') . '_mailbag';

    $xartable['mailbag_errors']     = $coprefix . '_errors';
    $xartable['mailbag_maillists']  = $coprefix . '_maillists';
    $xartable['mailbag_sblacklist'] = $coprefix . '_sblacklist';
    $xartable['mailbag_rblacklist'] = $coprefix . '_rblacklist';
    $xartable['mailbag_ublacklist'] = $coprefix . '_ublacklist';
 
    // Return table information
    return $xartable;
}
?>

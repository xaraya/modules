<?php
/**
 * Overview Menu
 */
function trackback_admin_create()
{
    // Security Check
    if(!xarSecurityCheck('Addtrackback')) return;
    // Parameters
    if (!xarVarFetch('pingurl', 'str:1', $pingurl)) return;
    if (!xarVarFetch('permalink', 'str:1', $permalink)) return;
    if (!xarVarFetch('title', 'str:1', $title, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('sitename', 'str:1', $sitename, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('excerpt', 'str:1:255', $excerpt, '', XARVAR_NOT_REQUIRED)) return;
    // Confirm authorisation code
    if (!xarSecConfirmAuthKey()) return;

    $res = xarModAPIFunc('trackback','admin','ping',
                         array('pingurl'    => $pingurl, 
                               'permalink'  => $permalink, 
                               'title'      => $title, 
                               'sitename'   => $sitename, 
                               'excerpt'    => $excerpt));
    if (!$res) return;

    // lets update status and display updated configuration
    xarResponseRedirect(xarModURL('trackback', 'admin', 'new')); 
    // Return
    return true;
}
?>
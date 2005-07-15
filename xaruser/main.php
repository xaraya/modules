<?php
/**
 * Main user function
 *
 */
function reports_user_main() 
{
    // TODO: Privileges!
    if(xarUserIsLoggedIn()) {
        return xarModFunc('reports','user','view');
    } else {
        xarResponseRedirect('/');
    }
}

?>
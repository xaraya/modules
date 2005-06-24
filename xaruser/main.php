<?php
// $Id: main.php,v 1.2 2005/01/26 08:45:25 michelv01 Exp $

function julian_user_main()
{    
    // redirect the user to the default view
    xarResponseRedirect(xarModURL('julian','user','month'));
}

?>

<?php

function release_admin_main()
{

    // Security Check
    xarResponseRedirect(xarModURL('release', 'admin', 'viewnotes'));
        
    return array();

}

?>
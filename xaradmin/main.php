<?php

function navigator_admin_main()
{
    return xarResponseRedirect(xarModURL('navigator', 'admin', 'modifyconfig'));
}

?>

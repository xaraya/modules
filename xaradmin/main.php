<?php
function security_admin_main($args)
{
    extract($args);
    
    xarResponseRedirect(xarModURL('security', 'admin', 'enablemodulesecurity'));
    
    return;
}
?>
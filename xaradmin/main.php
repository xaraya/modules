<?php
/**
    Main admin Security module function

    @return string module funtion output
*/
function security_admin_main($args)
{
    extract($args);
    
    xarResponseRedirect(xarModURL('security', 'admin', 'enablemodulesecurity'));
    
    return false;
}
?>
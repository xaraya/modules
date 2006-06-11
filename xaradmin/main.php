<?php
function netquery_admin_main()
{
    if (!xarSecurityCheck('AdminNetquery')) return;

    xarResponseRedirect(xarModURL('netquery', 'admin', 'config'));
    
    return $data;
}
?>
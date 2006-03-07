<?php
function window_admin_main()
{
    if (!xarSecurityCheck('AdminWindow')) return;
        return xarResponseRedirect(xarModURL('window', 'admin', 'general'));

}
?>
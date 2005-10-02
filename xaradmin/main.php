<?php
function dailydelicious_admin_main()
{
    if(!xarSecurityCheck('DailyDelicious')) return;
    if (xarModGetVar('adminpanels', 'overview') == 0){
        return array();
    } else {
        xarResponseRedirect(xarModURL('dailydelicious', 'admin', 'modifyconfig'));
    }
    return true;
}
?>
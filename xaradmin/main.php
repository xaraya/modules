<?php
function dailydelicious_admin_main()
{
    if(!xarSecurityCheck('DailyDelicious')) return;
   // xarResponseRedirect(xarModURL('dailydelicious', 'admin', 'modifyconfig'));
    return array();//true;
}
?>
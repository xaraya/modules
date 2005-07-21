<?php
/**
 *  Allows a user to modify their Calendar specific changes
 */
function calendar_user_updateconfig()
{
    xarVarFetch('cal_sdow','int:0:6',$cal_sdow,xarModGetUserVar('calendar','cal_sdow'));
    xarModSetUserVar('calendar','cal_sdow',$cal_sdow);

    xarVarFetch('default_view','str::',$default_view,xarModGetUserVar('calendar','default_view'));
    xarModSetUserVar('calendar','default_view',$default_view);

    xarResponseRedirect(xarModURL('calendar', 'user', 'modifyconfig'));
}

?>

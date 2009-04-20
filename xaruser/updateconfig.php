<?php
/**
 *  Allows a user to modify their Calendar specific changes
 */
function calendar_user_updateconfig()
{
    xarVarFetch('cal_sdow','int:0:6',$cal_sdow,xarModUserVars::get('calendar','cal_sdow'));
    xarModUserVars::set('calendar','cal_sdow',$cal_sdow);

    xarVarFetch('default_view','str::',$default_view,xarModUserVars::get('calendar','default_view'));
    xarModUserVars::set('calendar','default_view',$default_view);

    xarResponse::Redirect(xarModURL('calendar', 'user', 'modifyconfig'));
}

?>

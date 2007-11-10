<?php
/**
 *  Allows a user to modify their Calendar specific changes
 */
function calendar_user_modifyconfig()
{
    xarVarFetch('cal_sdow','int:0:6',$cal_sdow, xarModUserVars::get('calendar','cal_sdow'));
    xarVarFetch('default_view','int:0:6',$default_view, xarModUserVars::get('calendar','default_view'));
    return array(
        'cal_sdow'=>$cal_sdow,
        'default_view'=>$default_view
        );
}

?>

<?php

function calendar_user_main()
{
    xarResponse::Redirect(xarModURL('calendar','user',xarModUserVars::get('calendar','default_view')));
}
?>
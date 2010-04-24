<?php

function calendar_user_main()
{
    xarResponse::redirect(xarModURL('calendar','user',xarModUserVars::get('calendar','default_view')));
}
?>
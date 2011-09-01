<?php

function calendar_user_main()
{
    xarController::redirect(xarModURL('calendar','user',xarModUserVars::get('calendar','default_view')));
}
?>
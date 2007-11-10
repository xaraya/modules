<?php

function calendar_user_main()
{
    xarResponseRedirect(xarModURL('calendar','user',xarModUserVars::get('calendar','default_view')));
}
?>
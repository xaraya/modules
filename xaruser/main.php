<?php

function calendar_user_main()
{
    xarResponseRedirect(xarModURL('calendar','user',xarModGetUserVar('calendar','default_view')));
}
?>
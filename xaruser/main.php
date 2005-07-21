<?php
// $Id: main.php,v 1.2 2003/06/24 20:08:10 roger Exp $
function calendar_user_main()
{
    xarResponseRedirect(xarModURL('calendar','user',xarModGetUserVar('calendar','default_view')));
}
?>
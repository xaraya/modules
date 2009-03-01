<?php

function xtasks_admin_pnupgrade()
{
    if(!xarModAPIFunc('xtasks', 'admin', 'pnupgrade')) return;

    xarResponseRedirect(xarModURL('xtasks', 'admin', 'main'));

    return true;
}

?>
<?php
/**
    Run the Mailbag
   
    @returns void
*/
function mailbag_admin_runmailbag()
{
    xarModAPIFunc('mailbag', 'admin', 'mailbag');

    return;
}
?>
<?php
/**
    Runs the mailbag via the scheduler
*/
function mailbag_schedulerapi_runmailbag($args)
{
    extract ($args);

    $data = array();
    $data = xarModAPIFunc('mailbag','admin','runmailbag');


 return true;
}

?>
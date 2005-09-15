<?php
/**
    Count the numbe of tickets in the DB
*/
function helpdesk_userapi_count_tickets($args)
{
    $args['count'] = true;
    
    $count = xarModAPIFunc('helpdesk', 'user', 'gettickets', $args);
    
    return $count;    
}
?>
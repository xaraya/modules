<?php

function articles_userapi_getstates() 
{
    // Simplistic getstates function
    // Obviously needs to be smarter along with the other state functions
    return array(0 => xarML('Submitted'),
                 1 => xarML('Rejected'),
                 2 => xarML('Approved'),
                 3 => xarML('Frontpage'),
           //    4 => xarML('Unknown')
                 );
}
?>

<?php

function articles_userapi_getstates() 
{
    // Simplistic getstates function
    // Obviously needs to be smarter along with the other state functions
    return array(xarML('Submitted'),
                 xarML('Rejected'),
                 xarML('Approved'),
                 xarML('Frontpage'),
                 xarML('Unknown')
                 );
}
?>
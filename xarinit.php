<?php
// ----------------------------------------------------------------------
// Copyright (C) 2005 Marc Lutolf
// Purpose of file:  Initialisation functions for query module
// ----------------------------------------------------------------------

/**
 * initialise the system module
 */
function query_init()
{
    return true;
}

function query_activate()
{
    return true;
}

function query_upgrade($oldversion)
{
    switch($oldversion){
        case '1.0.0':

    }
// Upgrade successful
    return true;
}

function query_delete()
{
}
?>

<?php

/*
 * Add a new page type.
 */

function xarpages_admin_newtype()
{
    return(xarModFunc('xarpages', 'admin', 'modifytype'));
}

?>
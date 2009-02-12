<?php

function labaffiliate_admin_overview()
{
   /* Security Check */
    if (!xarSecurityCheck('AdminProgram',0)) return;

    $data = array();

    /* if there is a separate overview function return data to it
     * else just call the main function that displays the overview
     */

    return $data;
}

?>
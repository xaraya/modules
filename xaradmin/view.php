<?php
/**
 * This is a standard function to modify the configuration parameters of the
 * module
 */
function comments_admin_view()
{

    // Security Check
    if(!xarSecurityCheck('Comments-Admin'))
        return;

    return array();
}
?>
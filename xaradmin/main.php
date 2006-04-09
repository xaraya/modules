<?php
/**
 * @package ie7
 * @copyright (C) 2004 by Ninth Avenue Software Pty Ltd
 * @link http://www.ninthave.net
 * @author Roger Keays <roger.keays@ninthave.net>
 */

/*
 * Display the main admin page.
 */
function ie7_admin_main($args) 
{
    if (!xarSecurityCheck('AdminIE7',0)) return;    
    /* array of variables to pass to the admin-main template */
    return array();
}
?>

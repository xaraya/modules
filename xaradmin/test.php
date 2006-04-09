<?php
/**
 * @package ie7
 * @copyright (C) 2004 by Ninth Avenue Software Pty Ltd
 * @link http://www.ninthave.net
 * @author Roger Keays <roger.keays@ninthave.net>
 */

/*
 * Display the css menus demo.
 */
function ie7_admin_test($args)
{
    if (!xarSecurityCheck('AdminIE7')) return;
    //xarTplAddStyleLink('ie7', 'ie7test');

    /* array of variables to pass to the admin-main template */
    return array();
}
?>

<?php
/*
 * File: $Id: $
 *
 * SiteTools Module
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by jojodee
 * @link http://xaraya.athomeandabout.come
 *
 * @subpackage SiteTools module
 * @author Jo Dalle Nogare <http://xaraya.athomeandabout.com  contact:jojodee@xaraya.com>
*/


/**
 * generate the common admin menu configuration
 */
function sitetools_adminapi_menu()
{ 
    // Initialise the array that will hold the menu configuration
    $menu = array(); 
    // Specify the menu title to be used in your blocklayout template
    $menu['menutitle'] = xarML('SiteTools Administration'); 
    // Specify the menu labels to be used in your blocklayout template
    // Preset some status variable
    $menu['status'] = ''; 
    // Return the array containing the menu configuration
    return $menu;
} 
?>

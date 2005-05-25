<?php
// ----------------------------------------------------------------------
// Copyright (C) 2004: Marc Lutolf (marcinmilan@xaraya.com)
// Purpose of file:  Configuration functions for commerce
// ----------------------------------------------------------------------
//  based on:
//  (c) 2003 XT-Commerce
//  (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
//  (c) 2002-2003 osCommerce (oscommerce.sql,v 1.83); www.oscommerce.com
//  (c) 2003  nextcommerce (nextcommerce.sql,v 1.76 2003/08/25); www.nextcommerce.org
// ----------------------------------------------------------------------

function commerce_admin_languages()
{
    return xarModFunc('commerce','admin','commoninfo_object',array('objectname' => 'ice_languages'));
/* NOTES from the old code:
  
    GENERAL:
        - pay attention on removal of the default language
    
    ON INSERT:
        - create additional products_description records
        - create additional product option records
        - create additional categories_description records
        - create additional products_options_values records
        - create additional manufacturers_info records
        - create additional orders_status records
        - create additional customers status
    ON DELETE:
        - delete all the additional info above for the language deleted
*/        
  
}

?>
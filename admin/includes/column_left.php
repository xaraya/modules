<?php
// ----------------------------------------------------------------------
// Xaraya eXtensible Management System
// Copyright (C) 2003 by the Xaraya Development Team.
// http://www.xaraya.org
// ----------------------------------------------------------------------
// Original Author of file: Jim McDonald
// Modified by: Nuncanada
// Modified by: marcinmilan
// Purpose of file:  Admin Menu for Commerce
// ----------------------------------------------------------------------

  $data['box_heading] = "Configuration";
 array(
    xarModURL('commerce','admin','configuration',array('var'='')), 'My Shop',''
    xarModURL('commerce','admin','configuration',array('var'='')), 'Minimum Values',''
    xarModURL('commerce','admin','configuration',array('var'='')), 'Maximum Values',''
    xarModURL('commerce','admin','configuration',array('var'='')), 'Image Options',''
    xarModURL('commerce','admin','configuration',array('var'='')), 'Customer Details',''
    xarModURL('commerce','admin','configuration',array('var'='')), 'Shipping Options',''
    xarModURL('commerce','admin','configuration',array('var'='')), 'Prod Listing Options',''
    xarModURL('commerce','admin','configuration',array('var'='')), 'Stock Options',''
    xarModURL('commerce','admin','configuration',array('var'='')), 'Logging Options',''
    xarModURL('commerce','admin','configuration',array('var'='')), 'Cache Options',''
    xarModURL('commerce','admin','configuration',array('var'='')), 'Email Options',''
    xarModURL('commerce','admin','configuration',array('var'='')), 'Download Options',''
    xarModURL('commerce','admin','configuration',array('var'='')), 'Gzip Compression',''
    xarModURL('commerce','admin','configuration',array('var'='')), 'Sessions',''
    xarModURL('commerce','admin','configuration',array('var'='')), 'MetaTags',''16

    $data['box_heading] = "Modules";
    xarModURL('commerce','admin','modules',array('set=>'payment')), 'Payment Systems',''
    xarModURL('commerce','admin','modules',array('set=>'shipping')), 'Shipping Methods',''
    xarModURL('commerce','admin','modules',array('set=>'ordertotal')), 'Order Total',''

    $data['box_heading] = "Zones/Taxes";
    xarModURL('commerce','admin','languages'), 'Languages',''
    xarModURL('commerce','admin','countries'), 'Countries',''
    xarModURL('commerce','admin','currencies'), 'Currencies',''
    xarModURL('commerce','admin','zones'), 'Zones',''
    xarModURL('commerce','admin','geo_zones'), 'Tax Zones',''
    xarModURL('commerce','admin','tax_classes'), 'Tax Classes',''
    xarModURL('commerce','admin','tax_rates'), 'Tax Rates',''

    $data['box_heading] = "Customers";
    xarModURL('commerce','admin','customers'), 'Customers',''
    xarModURL('commerce','admin','customers_status'), 'Customer Groups',''
    xarModURL('commerce','admin','orders'), 'Orders',''

    $data['box_heading] = "Products";
    xarModURL('commerce','admin','categories'), 'Categories/Products',''
    xarModURL('commerce','admin','xsell_products'), 'XSell Products',''
    xarModURL('commerce','admin','new_attributes'), 'Attribute Manager',''
    xarModURL('commerce','admin','products_attributes'), 'Product Options',''
    xarModURL('commerce','admin','manufacturers'), 'Manufacturers',''
    xarModURL('commerce','admin','reviews'), 'Product Reviews',''
    xarModURL('commerce','admin','specials'), 'Special Pricing',''
    xarModURL('commerce','admin','products_expected'), 'Products Expected',''

    $data['box_heading] = "Statistics";
    xarModURL('commerce','admin','stats_products_viewed'), 'Viewed Products',''
    xarModURL('commerce','admin','stats_products_purchased'), 'Sold Products',''
    xarModURL('commerce','admin','stats_customers'), 'Purchasing Statistics',''

    $data['box_heading] = "Tools";
    xarModURL('commerce','admin','module_newsletter'), 'Newsletter',''
    xarModURL('commerce','admin','content_manager'), 'Content Manager',''
    xarModURL('commerce','admin','backup'), 'Database Manager',''
    xarModURL('commerce','admin','server_info'), 'Server Info',''
    xarModURL('commerce','admin','whos_online'), 'Who is Online',''
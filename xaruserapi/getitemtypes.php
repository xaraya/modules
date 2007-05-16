<?php

/**
 * utility function to retrieve the list of item types of this module (if any)
 *
 * @returns array
 * @return array containing the item types and their description
 */
function commerce_userapi_getitemtypes($args)
{
    $itemtypes = array();

// TODO: remove unused tables / objects + update URLs to whatever commerce GUI is relevant

    $prefix = xarDB::getPrefix();
    $modid = xarModGetIDFromName('commerce');

    $itemtypes[1] = array('label' => xarML('Address book'),
                          'title' => xarML('View #(1)','Address book'),
                     // TODO: replace with relevant URL in commerce later
                          'url'   => xarModURL('dynamicdata','admin','view',
                                               array('table' => $prefix . '_commerce_address_book'))
                                               // for tables converted to DD objects, use this
                                               //array('modid' => $modid, 'itemtype' => 1))
                         );


    $itemtypes[2] = array('label' => xarML('Address format'),
                          'title' => xarML('View #(1)','Address format'),
                     // TODO: replace with relevant URL in commerce later
                          'url'   => xarModURL('dynamicdata','admin','view',
                                               array('table' => $prefix . '_commerce_address_format'))
                                               // for tables converted to DD objects, use this
                                               //array('modid' => $modid, 'itemtype' => 2))
                         );


    $itemtypes[3] = array('label' => xarML('Admin access'),
                          'title' => xarML('View #(1)','Admin access'),
                     // TODO: replace with relevant URL in commerce later
                          'url'   => xarModURL('dynamicdata','admin','view',
                                               array('table' => $prefix . '_commerce_admin_access'))
                                               // for tables converted to DD objects, use this
                                               //array('modid' => $modid, 'itemtype' => 3))
                         );


    $itemtypes[4] = array('label' => xarML('Banktransfer'),
                          'title' => xarML('View #(1)','Banktransfer'),
                     // TODO: replace with relevant URL in commerce later
                          'url'   => xarModURL('dynamicdata','admin','view',
                                               array('table' => $prefix . '_commerce_banktransfer'))
                                               // for tables converted to DD objects, use this
                                               //array('modid' => $modid, 'itemtype' => 4))
                         );


    $itemtypes[5] = array('label' => xarML('Banners'),
                          'title' => xarML('View #(1)','Banners'),
                     // TODO: replace with relevant URL in commerce later
                          'url'   => xarModURL('dynamicdata','admin','view',
                                               array('table' => $prefix . '_commerce_banners'))
                                               // for tables converted to DD objects, use this
                                               //array('modid' => $modid, 'itemtype' => 5))
                         );


    $itemtypes[6] = array('label' => xarML('Banners history'),
                          'title' => xarML('View #(1)','Banners history'),
                     // TODO: replace with relevant URL in commerce later
                          'url'   => xarModURL('dynamicdata','admin','view',
                                               array('table' => $prefix . '_commerce_banners_history'))
                                               // for tables converted to DD objects, use this
                                               //array('modid' => $modid, 'itemtype' => 6))
                         );


    $itemtypes[7] = array('label' => xarML('Box align'),
                          'title' => xarML('View #(1)','Box align'),
                     // TODO: replace with relevant URL in commerce later
                          'url'   => xarModURL('dynamicdata','admin','view',
                                               array('table' => $prefix . '_commerce_box_align'))
                                               // for tables converted to DD objects, use this
                                               //array('modid' => $modid, 'itemtype' => 7))
                         );


    $itemtypes[8] = array('label' => xarML('Categories'),
                          'title' => xarML('View #(1)','Categories'),
                     // TODO: replace with relevant URL in commerce later
                          'url'   => xarModURL('dynamicdata','admin','view',
                                               array('table' => $prefix . '_commerce_categories'))
                                               // for tables converted to DD objects, use this
                                               //array('modid' => $modid, 'itemtype' => 8))
                         );


    $itemtypes[9] = array('label' => xarML('Categories description'),
                          'title' => xarML('View #(1)','Categories description'),
                     // TODO: replace with relevant URL in commerce later
                          'url'   => xarModURL('dynamicdata','admin','view',
                                               array('table' => $prefix . '_commerce_categories_description'))
                                               // for tables converted to DD objects, use this
                                               //array('modid' => $modid, 'itemtype' => 9))
                         );


    $itemtypes[10] = array('label' => xarML('Cm file flags'),
                          'title' => xarML('View #(1)','Cm file flags'),
                     // TODO: replace with relevant URL in commerce later
                          'url'   => xarModURL('dynamicdata','admin','view',
                                               array('table' => $prefix . '_commerce_cm_file_flags'))
                                               // for tables converted to DD objects, use this
                                               //array('modid' => $modid, 'itemtype' => 10))
                         );


    $itemtypes[11] = array('label' => xarML('Configuration'),
                          'title' => xarML('View #(1)','Configuration'),
                     // TODO: replace with relevant URL in commerce later
                          'url'   => xarModURL('dynamicdata','admin','view',
                                               array('table' => $prefix . '_commerce_configuration'))
                                               // for tables converted to DD objects, use this
                                               //array('modid' => $modid, 'itemtype' => 11))
                         );


    $itemtypes[12] = array('label' => xarML('Configuration group'),
                          'title' => xarML('View #(1)','Configuration group'),
                     // TODO: replace with relevant URL in commerce later
                          'url'   => xarModURL('dynamicdata','admin','view',
                                               array('table' => $prefix . '_commerce_configuration_group'))
                                               // for tables converted to DD objects, use this
                                               //array('modid' => $modid, 'itemtype' => 12))
                         );


    $itemtypes[13] = array('label' => xarML('Content manager'),
                          'title' => xarML('View #(1)','Content manager'),
                     // TODO: replace with relevant URL in commerce later
                          'url'   => xarModURL('dynamicdata','admin','view',
                                               array('table' => $prefix . '_commerce_content_manager'))
                                               // for tables converted to DD objects, use this
                                               //array('modid' => $modid, 'itemtype' => 13))
                         );


    $itemtypes[14] = array('label' => xarML('Counter'),
                          'title' => xarML('View #(1)','Counter'),
                     // TODO: replace with relevant URL in commerce later
                          'url'   => xarModURL('dynamicdata','admin','view',
                                               array('table' => $prefix . '_commerce_counter'))
                                               // for tables converted to DD objects, use this
                                               //array('modid' => $modid, 'itemtype' => 14))
                         );


    $itemtypes[15] = array('label' => xarML('Counter history'),
                          'title' => xarML('View #(1)','Counter history'),
                     // TODO: replace with relevant URL in commerce later
                          'url'   => xarModURL('dynamicdata','admin','view',
                                               array('table' => $prefix . '_commerce_counter_history'))
                                               // for tables converted to DD objects, use this
                                               //array('modid' => $modid, 'itemtype' => 15))
                         );


    $itemtypes[16] = array('label' => xarML('Countries'),
                          'title' => xarML('View #(1)','Countries'),
                     // TODO: replace with relevant URL in commerce later
                          'url'   => xarModURL('dynamicdata','admin','view',
                                               array('table' => $prefix . '_commerce_countries'))
                                               // for tables converted to DD objects, use this
                                               //array('modid' => $modid, 'itemtype' => 16))
                         );


    $itemtypes[17] = array('label' => xarML('Currencies'),
                          'title' => xarML('View #(1)','Currencies'),
                     // TODO: replace with relevant URL in commerce later
                          'url'   => xarModURL('dynamicdata','admin','view',
                                               array('table' => $prefix . '_commerce_currencies'))
                                               // for tables converted to DD objects, use this
                                               //array('modid' => $modid, 'itemtype' => 17))
                         );


    $itemtypes[18] = array('label' => xarML('Customers'),
                          'title' => xarML('View #(1)','Customers'),
                     // TODO: replace with relevant URL in commerce later
                          'url'   => xarModURL('dynamicdata','admin','view',
                                               array('table' => $prefix . '_commerce_customers'))
                                               // for tables converted to DD objects, use this
                                               //array('modid' => $modid, 'itemtype' => 18))
                         );


    $itemtypes[19] = array('label' => xarML('Customers basket'),
                          'title' => xarML('View #(1)','Customers basket'),
                     // TODO: replace with relevant URL in commerce later
                          'url'   => xarModURL('dynamicdata','admin','view',
                                               array('table' => $prefix . '_commerce_customers_basket'))
                                               // for tables converted to DD objects, use this
                                               //array('modid' => $modid, 'itemtype' => 19))
                         );


    $itemtypes[20] = array('label' => xarML('Customers basket attributes'),
                          'title' => xarML('View #(1)','Customers basket attributes'),
                     // TODO: replace with relevant URL in commerce later
                          'url'   => xarModURL('dynamicdata','admin','view',
                                               array('table' => $prefix . '_commerce_customers_basket_attributes'))
                                               // for tables converted to DD objects, use this
                                               //array('modid' => $modid, 'itemtype' => 20))
                         );


    $itemtypes[21] = array('label' => xarML('Customers info'),
                          'title' => xarML('View #(1)','Customers info'),
                     // TODO: replace with relevant URL in commerce later
                          'url'   => xarModURL('dynamicdata','admin','view',
                                               array('table' => $prefix . '_commerce_customers_info'))
                                               // for tables converted to DD objects, use this
                                               //array('modid' => $modid, 'itemtype' => 21))
                         );


    $itemtypes[22] = array('label' => xarML('Customers ip'),
                          'title' => xarML('View #(1)','Customers ip'),
                     // TODO: replace with relevant URL in commerce later
                          'url'   => xarModURL('dynamicdata','admin','view',
                                               array('table' => $prefix . '_commerce_customers_ip'))
                                               // for tables converted to DD objects, use this
                                               //array('modid' => $modid, 'itemtype' => 22))
                         );


    $itemtypes[23] = array('label' => xarML('Customers memo'),
                          'title' => xarML('View #(1)','Customers memo'),
                     // TODO: replace with relevant URL in commerce later
                          'url'   => xarModURL('dynamicdata','admin','view',
                                               array('table' => $prefix . '_commerce_customers_memo'))
                                               // for tables converted to DD objects, use this
                                               //array('modid' => $modid, 'itemtype' => 23))
                         );


    $itemtypes[24] = array('label' => xarML('Customers status'),
                          'title' => xarML('View #(1)','Customers status'),
                     // TODO: replace with relevant URL in commerce later
                          'url'   => xarModURL('dynamicdata','admin','view',
                                               array('table' => $prefix . '_commerce_customers_status'))
                                               // for tables converted to DD objects, use this
                                               //array('modid' => $modid, 'itemtype' => 24))
                         );


    $itemtypes[25] = array('label' => xarML('Customers status history'),
                          'title' => xarML('View #(1)','Customers status history'),
                     // TODO: replace with relevant URL in commerce later
                          'url'   => xarModURL('dynamicdata','admin','view',
                                               array('table' => $prefix . '_commerce_customers_status_history'))
                                               // for tables converted to DD objects, use this
                                               //array('modid' => $modid, 'itemtype' => 25))
                         );


    $itemtypes[26] = array('label' => xarML('Geo zones'),
                          'title' => xarML('View #(1)','Geo zones'),
                     // TODO: replace with relevant URL in commerce later
                          'url'   => xarModURL('dynamicdata','admin','view',
                                               array('table' => $prefix . '_commerce_geo_zones'))
                                               // for tables converted to DD objects, use this
                                               //array('modid' => $modid, 'itemtype' => 26))
                         );


    $itemtypes[27] = array('label' => xarML('Languages'),
                          'title' => xarML('View #(1)','Languages'),
                     // TODO: replace with relevant URL in commerce later
                          'url'   => xarModURL('dynamicdata','admin','view',
                                               array('table' => $prefix . '_commerce_languages'))
                                               // for tables converted to DD objects, use this
                                               //array('modid' => $modid, 'itemtype' => 27))
                         );


    $itemtypes[28] = array('label' => xarML('Manufacturers'),
                          'title' => xarML('View #(1)','Manufacturers'),
                     // TODO: replace with relevant URL in commerce later
                          'url'   => xarModURL('dynamicdata','admin','view',
                                               array('table' => $prefix . '_commerce_manufacturers'))
                                               // for tables converted to DD objects, use this
                                               //array('modid' => $modid, 'itemtype' => 28))
                         );


    $itemtypes[29] = array('label' => xarML('Manufacturers info'),
                          'title' => xarML('View #(1)','Manufacturers info'),
                     // TODO: replace with relevant URL in commerce later
                          'url'   => xarModURL('dynamicdata','admin','view',
                                               array('table' => $prefix . '_commerce_manufacturers_info'))
                                               // for tables converted to DD objects, use this
                                               //array('modid' => $modid, 'itemtype' => 29))
                         );


    $itemtypes[30] = array('label' => xarML('Media content'),
                          'title' => xarML('View #(1)','Media content'),
                     // TODO: replace with relevant URL in commerce later
                          'url'   => xarModURL('dynamicdata','admin','view',
                                               array('table' => $prefix . '_commerce_media_content'))
                                               // for tables converted to DD objects, use this
                                               //array('modid' => $modid, 'itemtype' => 30))
                         );


    $itemtypes[31] = array('label' => xarML('Module newsletter'),
                          'title' => xarML('View #(1)','Module newsletter'),
                     // TODO: replace with relevant URL in commerce later
                          'url'   => xarModURL('dynamicdata','admin','view',
                                               array('table' => $prefix . '_commerce_module_newsletter'))
                                               // for tables converted to DD objects, use this
                                               //array('modid' => $modid, 'itemtype' => 31))
                         );


    $itemtypes[32] = array('label' => xarML('Newsletters'),
                          'title' => xarML('View #(1)','Newsletters'),
                     // TODO: replace with relevant URL in commerce later
                          'url'   => xarModURL('dynamicdata','admin','view',
                                               array('table' => $prefix . '_commerce_newsletters'))
                                               // for tables converted to DD objects, use this
                                               //array('modid' => $modid, 'itemtype' => 32))
                         );


    $itemtypes[33] = array('label' => xarML('Newsletters history'),
                          'title' => xarML('View #(1)','Newsletters history'),
                     // TODO: replace with relevant URL in commerce later
                          'url'   => xarModURL('dynamicdata','admin','view',
                                               array('table' => $prefix . '_commerce_newsletters_history'))
                                               // for tables converted to DD objects, use this
                                               //array('modid' => $modid, 'itemtype' => 33))
                         );


    $itemtypes[34] = array('label' => xarML('Orders'),
                          'title' => xarML('View #(1)','Orders'),
                     // TODO: replace with relevant URL in commerce later
                          'url'   => xarModURL('dynamicdata','admin','view',
                                               array('table' => $prefix . '_commerce_orders'))
                                               // for tables converted to DD objects, use this
                                               //array('modid' => $modid, 'itemtype' => 34))
                         );


    $itemtypes[35] = array('label' => xarML('Orders products'),
                          'title' => xarML('View #(1)','Orders products'),
                     // TODO: replace with relevant URL in commerce later
                          'url'   => xarModURL('dynamicdata','admin','view',
                                               array('table' => $prefix . '_commerce_orders_products'))
                                               // for tables converted to DD objects, use this
                                               //array('modid' => $modid, 'itemtype' => 35))
                         );


    $itemtypes[36] = array('label' => xarML('Orders products attributes'),
                          'title' => xarML('View #(1)','Orders products attributes'),
                     // TODO: replace with relevant URL in commerce later
                          'url'   => xarModURL('dynamicdata','admin','view',
                                               array('table' => $prefix . '_commerce_orders_products_attributes'))
                                               // for tables converted to DD objects, use this
                                               //array('modid' => $modid, 'itemtype' => 36))
                         );


    $itemtypes[37] = array('label' => xarML('Orders products download'),
                          'title' => xarML('View #(1)','Orders products download'),
                     // TODO: replace with relevant URL in commerce later
                          'url'   => xarModURL('dynamicdata','admin','view',
                                               array('table' => $prefix . '_commerce_orders_products_download'))
                                               // for tables converted to DD objects, use this
                                               //array('modid' => $modid, 'itemtype' => 37))
                         );


    $itemtypes[38] = array('label' => xarML('Orders status'),
                          'title' => xarML('View #(1)','Orders status'),
                     // TODO: replace with relevant URL in commerce later
                          'url'   => xarModURL('dynamicdata','admin','view',
                                               array('table' => $prefix . '_commerce_orders_status'))
                                               // for tables converted to DD objects, use this
                                               //array('modid' => $modid, 'itemtype' => 38))
                         );


    $itemtypes[39] = array('label' => xarML('Orders status history'),
                          'title' => xarML('View #(1)','Orders status history'),
                     // TODO: replace with relevant URL in commerce later
                          'url'   => xarModURL('dynamicdata','admin','view',
                                               array('table' => $prefix . '_commerce_orders_status_history'))
                                               // for tables converted to DD objects, use this
                                               //array('modid' => $modid, 'itemtype' => 39))
                         );


    $itemtypes[40] = array('label' => xarML('Orders total'),
                          'title' => xarML('View #(1)','Orders total'),
                     // TODO: replace with relevant URL in commerce later
                          'url'   => xarModURL('dynamicdata','admin','view',
                                               array('table' => $prefix . '_commerce_orders_total'))
                                               // for tables converted to DD objects, use this
                                               //array('modid' => $modid, 'itemtype' => 40))
                         );


    $itemtypes[41] = array('label' => xarML('Products'),
                          'title' => xarML('View #(1)','Products'),
                     // TODO: replace with relevant URL in commerce later
                          'url'   => xarModURL('dynamicdata','admin','view',
                                               array('table' => $prefix . '_commerce_products'))
                                               // for tables converted to DD objects, use this
                                               //array('modid' => $modid, 'itemtype' => 41))
                         );


    $itemtypes[42] = array('label' => xarML('Products attributes'),
                          'title' => xarML('View #(1)','Products attributes'),
                     // TODO: replace with relevant URL in commerce later
                          'url'   => xarModURL('dynamicdata','admin','view',
                                               array('table' => $prefix . '_commerce_products_attributes'))
                                               // for tables converted to DD objects, use this
                                               //array('modid' => $modid, 'itemtype' => 42))
                         );


    $itemtypes[43] = array('label' => xarML('Products attributes download'),
                          'title' => xarML('View #(1)','Products attributes download'),
                     // TODO: replace with relevant URL in commerce later
                          'url'   => xarModURL('dynamicdata','admin','view',
                                               array('table' => $prefix . '_commerce_products_attributes_download'))
                                               // for tables converted to DD objects, use this
                                               //array('modid' => $modid, 'itemtype' => 43))
                         );


    $itemtypes[44] = array('label' => xarML('Products content'),
                          'title' => xarML('View #(1)','Products content'),
                     // TODO: replace with relevant URL in commerce later
                          'url'   => xarModURL('dynamicdata','admin','view',
                                               array('table' => $prefix . '_commerce_products_content'))
                                               // for tables converted to DD objects, use this
                                               //array('modid' => $modid, 'itemtype' => 44))
                         );


    $itemtypes[45] = array('label' => xarML('Products description'),
                          'title' => xarML('View #(1)','Products description'),
                     // TODO: replace with relevant URL in commerce later
                          'url'   => xarModURL('dynamicdata','admin','view',
                                               array('table' => $prefix . '_commerce_products_description'))
                                               // for tables converted to DD objects, use this
                                               //array('modid' => $modid, 'itemtype' => 45))
                         );


    $itemtypes[46] = array('label' => xarML('Products graduated prices'),
                          'title' => xarML('View #(1)','Products graduated prices'),
                     // TODO: replace with relevant URL in commerce later
                          'url'   => xarModURL('dynamicdata','admin','view',
                                               array('table' => $prefix . '_commerce_products_graduated_prices'))
                                               // for tables converted to DD objects, use this
                                               //array('modid' => $modid, 'itemtype' => 46))
                         );


    $itemtypes[47] = array('label' => xarML('Products notifications'),
                          'title' => xarML('View #(1)','Products notifications'),
                     // TODO: replace with relevant URL in commerce later
                          'url'   => xarModURL('dynamicdata','admin','view',
                                               array('table' => $prefix . '_commerce_products_notifications'))
                                               // for tables converted to DD objects, use this
                                               //array('modid' => $modid, 'itemtype' => 47))
                         );


    $itemtypes[48] = array('label' => xarML('Products options'),
                          'title' => xarML('View #(1)','Products options'),
                     // TODO: replace with relevant URL in commerce later
                          'url'   => xarModURL('dynamicdata','admin','view',
                                               array('table' => $prefix . '_commerce_products_options'))
                                               // for tables converted to DD objects, use this
                                               //array('modid' => $modid, 'itemtype' => 48))
                         );


    $itemtypes[49] = array('label' => xarML('Products options values'),
                          'title' => xarML('View #(1)','Products options values'),
                     // TODO: replace with relevant URL in commerce later
                          'url'   => xarModURL('dynamicdata','admin','view',
                                               array('table' => $prefix . '_commerce_products_options_values'))
                                               // for tables converted to DD objects, use this
                                               //array('modid' => $modid, 'itemtype' => 49))
                         );


    $itemtypes[50] = array('label' => xarML('Products options values to products options'),
                          'title' => xarML('View #(1)','Products options values to products options'),
                     // TODO: replace with relevant URL in commerce later
                          'url'   => xarModURL('dynamicdata','admin','view',
                                               array('table' => $prefix . '_commerce_products_options_values_to_products_options'))
                                               // for tables converted to DD objects, use this
                                               //array('modid' => $modid, 'itemtype' => 50))
                         );


    $itemtypes[51] = array('label' => xarML('Products to categories'),
                          'title' => xarML('View #(1)','Products to categories'),
                     // TODO: replace with relevant URL in commerce later
                          'url'   => xarModURL('dynamicdata','admin','view',
                                               array('table' => $prefix . '_commerce_products_to_categories'))
                                               // for tables converted to DD objects, use this
                                               //array('modid' => $modid, 'itemtype' => 51))
                         );


    $itemtypes[52] = array('label' => xarML('Products xsell'),
                          'title' => xarML('View #(1)','Products xsell'),
                     // TODO: replace with relevant URL in commerce later
                          'url'   => xarModURL('dynamicdata','admin','view',
                                               array('table' => $prefix . '_commerce_products_xsell'))
                                               // for tables converted to DD objects, use this
                                               //array('modid' => $modid, 'itemtype' => 52))
                         );


    $itemtypes[53] = array('label' => xarML('Reviews'),
                          'title' => xarML('View #(1)','Reviews'),
                     // TODO: replace with relevant URL in commerce later
                          'url'   => xarModURL('dynamicdata','admin','view',
                                               array('table' => $prefix . '_commerce_reviews'))
                                               // for tables converted to DD objects, use this
                                               //array('modid' => $modid, 'itemtype' => 53))
                         );


    $itemtypes[54] = array('label' => xarML('Reviews description'),
                          'title' => xarML('View #(1)','Reviews description'),
                     // TODO: replace with relevant URL in commerce later
                          'url'   => xarModURL('dynamicdata','admin','view',
                                               array('table' => $prefix . '_commerce_reviews_description'))
                                               // for tables converted to DD objects, use this
                                               //array('modid' => $modid, 'itemtype' => 54))
                         );


    $itemtypes[55] = array('label' => xarML('Sessions'),
                          'title' => xarML('View #(1)','Sessions'),
                     // TODO: replace with relevant URL in commerce later
                          'url'   => xarModURL('dynamicdata','admin','view',
                                               array('table' => $prefix . '_commerce_sessions'))
                                               // for tables converted to DD objects, use this
                                               //array('modid' => $modid, 'itemtype' => 55))
                         );


    $itemtypes[56] = array('label' => xarML('Specials'),
                          'title' => xarML('View #(1)','Specials'),
                     // TODO: replace with relevant URL in commerce later
                          'url'   => xarModURL('dynamicdata','admin','view',
                                               array('table' => $prefix . '_commerce_specials'))
                                               // for tables converted to DD objects, use this
                                               //array('modid' => $modid, 'itemtype' => 56))
                         );


    $itemtypes[57] = array('label' => xarML('Tax class'),
                          'title' => xarML('View #(1)','Tax class'),
                     // TODO: replace with relevant URL in commerce later
                          'url'   => xarModURL('dynamicdata','admin','view',
                                               array('table' => $prefix . '_commerce_tax_class'))
                                               // for tables converted to DD objects, use this
                                               //array('modid' => $modid, 'itemtype' => 57))
                         );


    $itemtypes[58] = array('label' => xarML('Tax rates'),
                          'title' => xarML('View #(1)','Tax rates'),
                     // TODO: replace with relevant URL in commerce later
                          'url'   => xarModURL('dynamicdata','admin','view',
                                               array('table' => $prefix . '_commerce_tax_rates'))
                                               // for tables converted to DD objects, use this
                                               //array('modid' => $modid, 'itemtype' => 58))
                         );


    $itemtypes[59] = array('label' => xarML('Whos online'),
                          'title' => xarML('View #(1)','Whos online'),
                     // TODO: replace with relevant URL in commerce later
                          'url'   => xarModURL('dynamicdata','admin','view',
                                               array('table' => $prefix . '_commerce_whos_online'))
                                               // for tables converted to DD objects, use this
                                               //array('modid' => $modid, 'itemtype' => 59))
                         );


    $itemtypes[60] = array('label' => xarML('Zones'),
                          'title' => xarML('View #(1)','Zones'),
                     // TODO: replace with relevant URL in commerce later
                          'url'   => xarModURL('dynamicdata','admin','view',
                                               array('table' => $prefix . '_commerce_zones'))
                                               // for tables converted to DD objects, use this
                                               //array('modid' => $modid, 'itemtype' => 60))
                         );


    $itemtypes[61] = array('label' => xarML('Zones to geo zones'),
                          'title' => xarML('View #(1)','Zones to geo zones'),
                     // TODO: replace with relevant URL in commerce later
                          'url'   => xarModURL('dynamicdata','admin','view',
                                               array('table' => $prefix . '_commerce_zones_to_geo_zones'))
                                               // for tables converted to DD objects, use this
                                               //array('modid' => $modid, 'itemtype' => 61))
                         );

    return $itemtypes;
}

?>

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
    include_once 'modules/xen/xarclasses/xenquery.php';
    include_once 'modules/commerce/xarclasses/object_info.php';
    include_once 'modules/commerce/xarclasses/split_page_results.php';
    $xartables = xarDBGetTables();
    $localeinfo = xarLocaleGetInfo(xarMLSGetSiteLocale());

    if(!xarVarFetch('action', 'str',  $action, NULL, XARVAR_DONT_SET)) {return;}
    if(!xarVarFetch('page',   'int',  $page, 1, XARVAR_NOT_REQUIRED)) {return;}
    if(!xarVarFetch('cID',    'int',  $cID, NULL, XARVAR_DONT_SET)) {return;}
    if (isset($action)) {
        switch ($action) {
            case 'insert':
                if(!xarVarFetch('nanme','str',$name)) {return;}
                if(!xarVarFetch('code','str',$code)) {return;}
                if(!xarVarFetch('image','str',$image)) {return;}
                if(!xarVarFetch('directory','str',  $directory)) {return;}
                if(!xarVarFetch('sort_order','str',  $sort_order)) {return;}
                if(!xarVarFetch('charset','str',  $charset)) {return;}
                $q = new xenQuery('INSERT', $xartables['commerce_languages']);
                $q->addfield('name',$name);
                $q->addfield('code',$code);
                $q->addfield('image',$image);
                $q->addfield('directory',$directory);
                $q->addfield('sort_order',$sort_order);
                $q->addfield('charset',$charset);
                $q->run();

      // create additional categories_description records
                $q = new xenQuery('SELECT',$xartables['commerce_categories'],'c');
                $q->addtable($xartables['commerce_categories_description'],'cd');
                $q->addfields('c.categories_id', 'cd.categories_name');
                $q->join('c.categories_id','cd.categories_id');
                $q->eq('cd.language_id',$_SESSION['languages_id']);
                $q->run();
                foreach ($q->output() as $category) {
                    $q = new xenQuery('INSERT', $xartables['commerce_categories_description']);
                    $q->addfield('categories_id',$category['categories_id']);
                    $q->addfield('language_id',$insert_id);
                    $q->addfield('categories_name',$category['categories_name']);
                    $q->run();
                }

      // create additional products_description records
                $q = new xenQuery('SELECT',$xartables['commerce_products'],'p');
                $q->addtable($xartables['commerce_products_description'],'pd');
                $q->addfields('p.products_id', 'pd.products_name', 'pd.products_description', 'pd.products_url');
                $q->join('p.products_id','pd.products_id');
                $q->eq('pd.language_id',$_SESSION['languages_id']);
                $q->run();
                foreach ($q->output() as $products) {
                    $q = new xenQuery('INSERT', $xartables['commerce_products_description']);
                    $q->addfield('products_id',$products['products_id']);
                    $q->addfield('language_id',$insert_id);
                    $q->addfield('products_name',$products['products_name']);
                    $q->addfield('products_description',$products['products_description']);
                    $q->addfield('products_url',$products['products_url']);
                    $q->run();
                }

      // create additional products_options records
                $q = new xenQuery('SELECT',$xartables['commerce_products_options']);
                $q->addfields('products_options_id', 'products_options_name');
                $q->eq('language_id',$_SESSION['languages_id']);
                $q->run();
                foreach ($q->output() as $products_options) {
                    $q = new xenQuery('INSERT', $xartables['commerce_products_options']);
                    $q->addfield('products_options_id',$products_options['products_options_id']);
                    $q->addfield('language_id',$insert_id);
                    $q->addfield('products_options_name',$products_options['products_options_name']);
                    $q->run();
                }

      // create additional products_options_values records
                $q = new xenQuery('SELECT',$xartables['commerce_products_options_values']);
                $q->addfields('products_options_values_id', 'products_options_values_name');
                $q->eq('language_id',$_SESSION['languages_id']);
                $q->run();
                foreach ($q->output() as $products_options_values) {
                    $q = new xenQuery('INSERT', $xartables['commerce_products_description']);
                    $q->addfield('products_options_values_id',$products_options['products_options_values_id']);
                    $q->addfield('language_id',$insert_id);
                    $q->addfield('products_options_values_name',$products_options['products_options_values_name']);
                    $q->run();
                }

      // create additional manufacturers_info records
                $q = new xenQuery('SELECT',$xartables['commerce_manufacturers'],'m');
                $q->addtable($xartables['commerce_manufacturers_info'],'mi');
                $q->addfields('m.manufacturers_id', 'mi.manufacturers_url');
                $q->join('m.manufacturers_id','mi.manufacturers_id');
                $q->eq('mi.language_id',$_SESSION['languages_id']);
                $q->run();
                foreach ($q->output() as $manufacturers) {
                    $q = new xenQuery('INSERT', $xartables['commerce_manufacturers_info']);
                    $q->addfield('manufacturers_id',$manufacturers['manufacturers_id']);
                    $q->addfield('language_id',$insert_id);
                    $q->addfield('manufacturers_url',$manufacturers['manufacturers_url']);
                    $q->run();
                }

      // create additional orders_status records
                $q = new xenQuery('SELECT',$xartables['commerce_orders_status']);
                $q->addfields('orders_status_id', 'orders_status_name');
                $q->eq('language_id',$_SESSION['languages_id']);
                $q->run();
                foreach ($q->output() as $orders_status) {
                    $q = new xenQuery('INSERT', $xartables['orders_status']);
                    $q->addfield('orders_status_id',$orders_status['orders_status_id']);
                    $q->addfield('language_id',$insert_id);
                    $q->addfield('orders_status_name',$orders_status['orders_status_name']);
                    $q->run();
                }

      // create additional customers status
                $q = new xenQuery('SELECT',$xartables['commerce_customers_status']);
                $q->addfields('DISTINCT customers_status_id');
                $q->run();
                foreach ($q->output() as $data) {
                    $q1 = new xenQuery('SELECT',$xartables['commerce_customers_status']);
                    $q1->eq('customers_status_id',$data['customers_status_id']);
                    $q1->run();

                    $group_data = $q1->row();
                    $q->addfield('customers_status_id',$data['customers_status_id']);
                    $q->addfield('language_id',$insert_id);
                    $q->addfield('customers_status_name',$group_data['customers_status_name']);
                    $q->addfield('customers_status_public',$group_data['customers_status_public']);
                    $q->addfield('customers_status_image',$group_data['customers_status_image']);
                    $q->addfield('customers_status_discount',$group_data['customers_status_discount']);
                    $q->addfield('customers_status_ot_discount_flag',$group_data['customers_status_ot_discount_flag']);
                    $q->addfield('customers_status_ot_discount',$group_data['customers_status_ot_discount']);
                    $q->addfield('customers_status_graduated_prices',$group_data['customers_status_graduated_prices']);
                    $q->addfield('customers_status_show_price',$group_data['customers_status_show_price']);
                    $q->addfield('customers_status_show_price_tax',$group_data['customers_status_show_price_tax']);
                    $q->addfield('customers_status_add_tax_ot',$group_data['customers_status_add_tax_ot']);
                    $q->addfield('customers_status_payment_unallowed',$group_data['customers_status_payment_unallowed']);
                    $q->addfield('customers_status_shipping_unallowed',$group_data['customers_status_shipping_unallowed']);
                    $q->addfield('customers_status_discount_attributes',$group_data['customers_status_discount_attributes']);

    xtc_db_perform(TABLE_CUSTOMERS_STATUS, $c_data);

                }

                if(!xarVarFetch('default','str',$default)) {return;}
                if ($default == 'on') {
                    $q = new xenQuery('UPDATE', $xartables['commerce_configuration']);
                    $q->addfield('configuration_value', $code);
                    $q->eq('configuration_key',DEFAULT_LANGUAGE);
                    $q->run();
                }

                xarResponseRedirect(xarModURL('commerce','admin','languages',array('page' => $page,'cID' => $insert_id)));
                break;
            case 'save':
                if(!xarVarFetch('nanme','str',$name)) {return;}
                if(!xarVarFetch('code','str',$code)) {return;}
                if(!xarVarFetch('image','str',$image)) {return;}
                if(!xarVarFetch('directory','str',  $directory)) {return;}
                if(!xarVarFetch('sort_order','str',  $sort_order)) {return;}
                if(!xarVarFetch('charset','str',  $charset)) {return;}
                $q = new xenQuery('UPDATE', $xartables['commerce_languages']);
                $q->addfield('name',$name);
                $q->addfield('code',$code);
                $q->addfield('image',$image);
                $q->addfield('directory',$directory);
                $q->addfield('sort_order',$sort_order);
                $q->addfield('charset',$charset);
                $q->eq('languages_id',$cID);
                $q->run();

                if(!xarVarFetch('default','str',$default)) {return;}
                if ($default == 'on') {
                    $q = new xenQuery('UPDATE', $xartables['commerce_configuration']);
                    $q->addfield('configuration_value', $code);
                    $q->eq('configuration_key',DEFAULT_LANGUAGE);
                    $q->run();
                }

                xarResponseRedirect(xarModURL('commerce','admin','languages',array('page' => $page,'cID' => $cID)));
            case 'deleteconfirm':
                $q = new xenQuery('SELECT', $xartables['commerce_languages']);
                $q->addfield('languages_id');
                $q->eq('code',DEFAULT_CURRENCY);
                $q->run();
                $lng = $q->row();
                if ($lng['languages_id'] == $cID) {
                    $q = new xenQuery('UPDATE', $xartables['commerce_configuration']);
                    $q->addfield('configuration_value','');
                    $q->eq('configuration_key',DEFAULT_CURRENCY);
                    $q->run();
                }

                $q = new xenQuery('DELETE', $xartables['commerce_categories_description']);
                $q->eq('languages_id',$cID);
                $q->run();
                $q->settable($xartables['commerce_products_description']);
                $q->run();
                $q->settable($xartables['commerce_products_options']);
                $q->run();
                $q->settable($xartables['commerce_products_options_values']);
                $q->run();
                $q->settable($xartables['commerce_manufacturers_info']);
                $q->run();
                $q->settable($xartables['commerce_orders_status']);
                $q->run();
                $q->settable($xartables['commerce_languages']);
                $q->run();
                $q->settable($xartables['commerce_content_manager']);
                $q->run();
                $q->settable($xartables['commerce_products_content']);
                $q->run();
                $q->settable($xartables['commerce_products_description']);
                $q->run();
                $q->settable($xartables['commerce_customers_status']);
                $q->run();

                xarResponseRedirect(xarModURL('commerce','admin','languages',array('page' => $page)));
                break;
            case 'delete':
                $q = new xenQuery('SELECT', $xartables['commerce_languages'],array('code'));
                $q->eq('languages_id',$cID);
                $q->run();
                $lng = $q->row();
                if ($lng['code'] == $localeinfo['lang']) {
                    $remove_language = false;
//                    $messageStack->add(ERROR_REMOVE_DEFAULT_LANGUAGE, 'error');
                }
                break;
        }
    }

    $data['language'] = $localeinfo['lang'] . "_" . $localeinfo['country'];
    $data['lang'] = $localeinfo['lang'];

    $q = new xenQuery('SELECT');
    $q->addtable($xartables['commerce_languages']);
    $q->addfields(array('languages_id', 'name', 'code', 'image', 'directory', 'sort_order','language_charset'));
    $q->setorder('sort_order');
    $q->setrowstodo(xarModGetVar('commerce', 'itemsperpage'));
    $q->setstartat(($page - 1) * xarModGetVar('commerce', 'itemsperpage') + 1);
    $q->run();

    $pager = new splitPageResults($page,
                                  $q->getrows(),
                                  xarModURL('commerce','admin','languages'),
                                  xarModGetVar('commerce', 'itemsperpage')
                                 );
    $data['pagermsg'] = $pager->display_count('Displaying #(1) to #(2) (of #(3) languages)');
    $data['displaylinks'] = $pager->display_links();

    $items =$q->output();
    $limit = count($items);
    for ($i=0;$i<$limit;$i++) {
        if ((!isset($cID) || $cID == $items[$i]['languages_id']) && !isset($cInfo) && (substr($action, 0, 3) != 'new')) {
           $cInfo = new objectInfo($items[$i]);
            $items[$i]['url'] = xarModURL('commerce','admin','languages',array('page' => $page,'cID' => $cInfo->languages_id, 'action' => 'edit'));
        }
        else {
            $items[$i]['url'] = xarModURL('commerce','admin','languages',array('page' => $page, 'cID' => $items[$i]['languages_id']));
        }
    }
    $data['items'] = $items;
    $data['cInfo'] = isset($cInfo) ? get_object_vars($cInfo) : '';
    $data['page'] = $page;
    $data['action'] = $action;

    return $data;

}
?>
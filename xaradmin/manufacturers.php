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

function commerce_admin_manufacturers()
{
    include_once 'modules/xen/xarclasses/xenquery.php';
    include_once 'modules/commerce/xarclasses/object_info.php';
    include_once 'modules/commerce/xarclasses/split_page_results.php';
    $xartables = xarDBGetTables();

    if(!xarVarFetch('action', 'str',  $action, NULL, XARVAR_DONT_SET)) {return;}
    if(!xarVarFetch('page',   'int',  $page, 1, XARVAR_NOT_REQUIRED)) {return;}
    if(!xarVarFetch('cID',    'int',  $cID, NULL, XARVAR_DONT_SET)) {return;}
    if (isset($action)) {
        switch ($action) {
            case 'insert':
                if(!xarVarFetch('manufacturers_name','str',$manufacturers_name)) {return;}
                $q = new xenQuery('INSERT', $xartables['commerce_manufacturers']);
                $q->addfield('manufacturers_name',$manufacturers_name);
                $q->addfield('date_added',mktime());
              $languages = xarModAPIFunc('commerce','user','get_languages');
              for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
                if(!xarVarFetch('manufacturers_url','str',$manufacturers_url_array[$language_id])) {return;}
                $language_id = $languages[$i]['id'];
                $q->addfield('manufacturers_url',$manufacturers_url_array[$language_id]);
                $q->run();

                $q = new xenQuery('INSERT', $xartables['commerce_manufacturers_info']);
                $q->addfield('languages_id',$language_id);
                $q->run();
              }
                xarResponseRedirect(xarModURL('commerce','admin','manufacturers'));
                break;
            case 'save':
                $q = new xenQuery('UPDATE', $xartables['commerce_manufacturers']);
                $q->addfield('manufacturers_name',$manufacturers_name);
                $q->addfield('last_modified',mktime());
                $q->eq('manufacturers_id',$cID);
              $languages = xarModAPIFunc('commerce','user','get_languages');
              for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
                if(!xarVarFetch('manufacturers_url','str',$manufacturers_url_array[$language_id])) {return;}
                $language_id = $languages[$i]['id'];
                $q->addfield('manufacturers_url',$manufacturers_url_array[$language_id]);
                $q->eq('languages_id',$language_id);
                $q->run();

                $q = new xenQuery('INSERT', $xartables['commerce_manufacturers_info']);
                $q->eq('manufacturers_id',$manufacturers_id);
                $q->eq('languages_id',$language_id);
                $q->run();
              }
                /*      if ($manufacturers_image = new upload('manufacturers_image', DIR_FS_CATALOG_IMAGES)) {
                //        new xenQuery("update " . TABLE_MANUFACTURERS . " set manufacturers_image = '" . $manufacturers_image->filename . "' where manufacturers_id = '" . xtc_db_input($manufacturers_id) . "'");
                //      }


                //      if (USE_CACHE == 'true') {
                //        xtc_reset_cache_block('manufacturers');
                //      }
                */
                xarResponseRedirect(xarModURL('commerce','admin','manufacturers',array('page' => $page,'cID' => $cID)));
                break;
            case 'deleteconfirm':
                if(!xarVarFetch('delete_image','str',$delete_image)) {return;}
                if ($delete_image == 'on') {
                    $q = new xenQuery('SELECT', $xartables['commerce_manufacturers'],array('manufacturers_image'));
                    $q->eq('manufacturers_id',$cID);
                    $q->run();
                    $manufacturer = $q->row();
                    $image_location = 'modules/commerce/xarimages/' . $manufacturer['manufacturers_image'];
                    if (file_exists($image_location)) @unlink($image_location);
                }
                $q = new xenQuery('DELETE', $xartables['commerce_manufacturers']);
                $q->eq('manufacturers_id',$cID);
                $q->run();
                $q = new xenQuery('DELETE', $xartables['commerce_manufacturers_info']);
                $q->eq('manufacturers_id',$cID);
                $q->run();
                if(!xarVarFetch('delete_products','str',$delete_products)) {return;}
                if ($delete_products == 'on') {
                    $q = new xenQuery('SELECT', $xartables['commerce_products']);
                    $q->eq('manufacturers_id',$cID);
                    $q->run();
                    foreach ($q->output() as $product) {
                      xarModAPIFunc('commerce','admin','remove_product',array('id' =>$products['products_id']));
                    }
                }
                else {
                    $q = new xenQuery('UPDATE', $xartables['commerce_products']);
                    $q->addfield('manufacturers_id','');
                    $q->eq('manufacturers_id',$cID);
                    $q->run();
                }
//      if (USE_CACHE == 'true') {
//        xtc_reset_cache_block('manufacturers');
//      }

                xarResponseRedirect(xarModURL('commerce','admin','manufacturers',array('page' => $page)));
                break;
        }
    }

    $localeinfo = xarLocaleGetInfo(xarMLSGetSiteLocale());
    $data['language'] = $localeinfo['lang'] . "_" . $localeinfo['country'];

    $q = new xenQuery('SELECT',$xartables['commerce_manufacturers']);
    $q->addfields(array('manufacturers_id', 'manufacturers_name', 'manufacturers_image', 'date_added', 'last_modified'));
    $q->setorder('manufacturers_name');
    $q->setrowstodo(xarModGetVar('commerce', 'itemsperpage'));
    $q->setstartat(($page - 1) * xarModGetVar('commerce', 'itemsperpage') + 1);
    $q->run();

    $pager = new splitPageResults($page,
                                  $q->getrows(),
                                  xarModURL('commerce','admin','manufacturers'),
                                  xarModGetVar('commerce', 'itemsperpage')
                                 );
    $data['pagermsg'] = $pager->display_count('Displaying #(1) to #(2) (of #(3) manufacturers)');
    $data['displaylinks'] = $pager->display_links();

    $items =$q->output();
    $limit = count($items);
    for ($i=0;$i<$limit;$i++) {
        if ((!isset($cID) || $cID == $items[$i]['manufacturers_id']) && !isset($cInfo) && (substr($action, 0, 3) != 'new')) {

            $manufacturer_products_query = new xenQuery("select count(*) as products_count from " . TABLE_PRODUCTS . " where manufacturers_id = '" . $manufacturers['manufacturers_id'] . "'");
            $q = new xenQuery('SELECT',$xartables['commerce_products']);
            $q->eq('manufacturers_id',$cID);
            $q->addfields('count(*) as products_count');
            $q->run();
            $manufacturer_products = $q->row();
            $items = array_merge($items,$manufacturer_products);
            $cInfo = new objectInfo($items[$i]);
            $items[$i]['url'] = xarModURL('commerce','admin','manufacturers',array('page' => $page,'cID' => $cInfo->manufacturers_id, 'action' => 'edit'));
        }
        else {
            $items[$i]['url'] = xarModURL('commerce','admin','manufacturers',array('page' => $page, 'cID' => $items[$i]['manufacturers_id']));
        }
    }

    $data['items'] = $items;
    $data['cInfo'] = isset($cInfo) ? get_object_vars($cInfo) : '';
    $data['page'] = $page;
    $data['action'] = $action;

    $languages = xarModAPIFunc('commerce','user','get_languages');
    $data['languages'] = $languages;
//echo var_dump($languages);exit;
    return $data;
}
?>
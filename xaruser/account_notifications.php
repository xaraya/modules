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

function commerce_user_account_notifications()
{
    include_once 'modules/xen/xarclasses/xenquery.php';
    $xartables = xarDBGetTables();
    $localeinfo = xarLocaleGetInfo(xarMLSGetSiteLocale());
    $data['language'] = $localeinfo['lang'] . "_" . $localeinfo['country'];
    $currentlang = xarModAPIFunc('commerce','user','get_language',array('locale' => $data['language']));

    if (!xarUserIsLoggedIn()) {
        xarResponseRedirect(xarModURL('commerce','user','login'));
    }

    $q = new xenQuery('SELECT',$xartables['commerce_customers_info'], 'global_product_notifications');
    $q->eq('customers_info_id', xarSessionGetVar('uid'));
    if(!$q->run()) return;
    $global = $q->row();
    $data['global_product_notifications'] = $global['global_product_notifications'];

    if(!xarVarFetch('action',   'str',  $action, '', XARVAR_NOT_REQUIRED)) {return;}
    if(!xarVarFetch('product_global',   'str',  $product_global, '', XARVAR_NOT_REQUIRED)) {return;}
    if(!xarVarFetch('products',   'array',  $products, array(), XARVAR_NOT_REQUIRED)) {return;}

    if ($action == 'process') {
        if (!is_numeric($product_global)) $product_global = 0;

        if ($product_global != $global['global_product_notifications']) {
            $product_global = (($global['global_product_notifications'] == '1') ? '0' : '1');

            $q = new xenQuery('UPDATE', $xartables['commerce_customers_info']);
            $q->addfield('global_product_notifications', (int)$product_global);
            $q->eq('customers_info_id', xarSessionGetVar('uid'));
        } elseif (sizeof($products) > 0) {
            $products_parsed = array();
            for ($i=0, $n=sizeof($products); $i<$n; $i++) {
                if (is_numeric($products[$i])) {
                    $products_parsed[] = $products[$i];
                }
            }
            if (sizeof($products_parsed) > 0) {
                $q = new xenQuery('SELECT', $xartables['commerce_customers_info'], 'count(*) as total');
                $q->eq('customers_info_id', xarSessionGetVar('uid'));
                $q->notin('products_id', $products_parsed);
                if(!$q->run()) return;
                $check = $q->row();
                if ($check['total'] > 0) {
                    $q = new xenQuery('DELETE', $xartables['commerce_customers_info']);
                    $q->eq('customers_info_id', xarSessionGetVar('uid'));
                    $q->notin('products_id', $products_parsed);
                }
            }
        } else {
            $q = new xenQuery('SELECT', $xartables['commerce_customers_info'], 'count(*) as total');
            $q->eq('customers_info_id', xarSessionGetVar('uid'));
            if(!$q->run()) return;
            $check = $q->row();
            if ($check['total'] > 0) {
                $q = new xenQuery('DELETE', $xartables['commerce_customers_info']);
                $q->eq('customers_info_id', xarSessionGetVar('uid'));
            }
        }
//    $messageStack->add_session('account', SUCCESS_NOTIFICATIONS_UPDATED, 'success');

        xarRedirectResponse(xarModURL('commerce','user','account'));
    }

//  $breadcrumb->add(NAVBAR_TITLE_1_ACCOUNT_NOTIFICATIONS, xarModURL('commerce','user','account'));
//  $breadcrumb->add(NAVBAR_TITLE_2_ACCOUNT_NOTIFICATIONS, xarModURL('commerce','user','account_notifications'));

// require(DIR_WS_INCLUDES . 'header.php');

    if ($global['global_product_notifications'] != '1') {
        $data['global_notification'] = 0;
    } else {
        $data['global_notification'] = 1;
    }
    if ($global['global_product_notifications'] != '1') {

        $q = new xenQuery('SELECT', $xartables['commerce_customers_info'], 'count(*) as total');
        $q->eq('customers_info_id', xarSessionGetVar('uid'));
        if(!$q->run()) return;
        $products_check = $q->row();
        if ($products_check['total'] > 0) {

            $counter = 0;
            $notifications_products='<table width="100%" border="0" cellspacing="0" cellpadding="0">';
            $q = new xenQuery('SELECT');
            $q->addtable($xartables['commerce_products_description'],'pd');
            $q->addtable($xartables['commerce_products_notifications'],'pn');
            $q->eq('pn.customers_id', xarSessionGetVar('uid'));
            $q->join('pn.products_id','pd.products_id');


            $q->eq('pd.language_id', $currentlang['id']);
            $q->setorder('pd.products_name');
            if(!$q->run()) return;
/*            foreach ($q->output() as $products) {
                $notifications_products.= '<tr class="moduleRow" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="checkBox(\'products['.$counter.']\')">
                <td class="main" width="30">'.xtc_draw_checkbox_field('products[' . $counter . ']', $products['products_id'], true, 'onclick="checkBox(\'products[' . $counter . ']\')"').'</td>
                <td class="main"><b>'.$products['products_name'].'</b></td>
                </tr> ';
                $counter++;
            }
            $notifications_products.= '</table>';
            $data['products_notification'] = $notifications_products;
            */
        } else {
        }
    }


return $data;
}
?>
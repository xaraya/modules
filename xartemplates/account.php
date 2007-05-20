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

function commerce_user_account()
{
    sys::import('modules.xen.xarclasses.xenquery');
    $xartables = xarDB::getTables();
    $languages = xarModAPIFunc('commerce','user','get_languages');
    $localeinfo = xarLocaleGetInfo(xarMLSGetSiteLocale());
    $data['language'] = $localeinfo['lang'] . "_" . $localeinfo['country'];
    $currentlang = xarModAPIFunc('commerce','user','get_language',array('locale' => $data['language']));

  // include needed functions
  require_once(DIR_FS_INC . 'xtc_count_customer_orders.inc.php');

    if (!xarUserIsLoggedIn)) {
        xarRedirectResponse(xarModURL('commerce','user','login'));
    }

  $breadcrumb->add(NAVBAR_TITLE_ACCOUNT, xarModURL('commerce','user','account'));

    if ($messageStack->size('account') > 0) {

        $data['error_message'] = $messageStack->output('account');

    }
    if (xtc_count_customer_orders() > 0) {
        $q = new xenQuery('SELECT');
        $q->addtable(xartables['commerce_orders'], 'o');
        $q->addtable(xartables['commerce_orders_total'], 'ot');
        $q->addtable(xartables['commerce_orders_status'], 's');
        $q->addields('o.orders_id AS ',
                    'o.date_purchased AS date_purchased',
                    'o.delivery_name AS delivery_name',
                    'o.delivery_country AS delivery_country',
                    'o.billing_name AS billing_name',
                    'o.billing_country AS billing_country',
                    'ot.text as order_total',
                    's.orders_status_name AS orders_status_name'

        );
        $q->eq('s.language_id', $currentlang['id']);
        $q->eq('o.customers_id', xarSession::getVar('uid'));
        $q->eq('ot.class', 'ot_total');
        $q->join('o.orders_status','s.orders_status_id');
        $q->join('o.orders_id','ot.orders_id');
        $q->addorder('orders_id', 'desc');
        $q->setrowstodo(3);
        if(!$q->run()) return;
        foreach ($q->output() as $orders) {
            if ($orders['delivery_name'] != null) {
                $order_name = $orders['delivery_name'];
                $order_country = $orders['delivery_country'];
            } else {
                $order_name = $orders['billing_name'];
                $order_country = $orders['billing_country'];
            }
            $order_content[]=array(
                            'order_id' =>$orders['orders_id'],
                            'order_date' =>xarModAPIFunc('commerce','user','date_short',array('raw_date' =>$orders['date_purchased'])),
                            'order_status' =>$orders['orders_status_name'],
                            'order_total' =>$orders['order_total'],
                            'order_link' => xarModURL('commerce','user','account_history_info', array('order_id' => $orders['orders_id'])) ,
                            'order_button' => '<a href="'. xarModURL('commerce','user','account_history_info, array('order_id' => $orders['orders_id'])) . '">' .
                            xarModAPIFunc('commerce','user','image',array('src' => xarTplGetImage('buttons/' . xarSession::getVar('language') . '/'.'small_view.gif'),'alt' => SMALL_IMAGE_BUTTON_VIEW);
                        . '</a>');
        }

    }
  $data['order_content'] = $order_content;
  $data['language'] = $_SESSION['language'];
  return data;
}
?>
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

function commerce_admin_currencies()
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
                if(!xarVarFetch('title','str',$title)) {return;}
                if(!xarVarFetch('code','str',$code)) {return;}
                if(!xarVarFetch('symbol_left','str',$symbol_left)) {return;}
                if(!xarVarFetch('symbol_right','str',  $symbol_right)) {return;}
                if(!xarVarFetch('decimal_point','str',  $decimal_point)) {return;}
                if(!xarVarFetch('thousands_point','str',  $thousands_point)) {return;}
                if(!xarVarFetch('decimal_places','int',  $decimal_places,0,XARVAR_NOT_REQUIRED)) {return;}
                if(!xarVarFetch('value','float',  $value)) {return;}
                $q = new xenQuery('INSERT', $xartables['commerce_currencies']);
                $q->addfield('title',$title);
                $q->addfield('code',$code);
                $q->addfield('symbol_left',$symbol_left);
                $q->addfield('symbol_right',$symbol_right);
                $q->addfield('decimal_point',$decimal_point);
                $q->addfield('thousands_point',$thousands_point);
                $q->addfield('decimal_places',$decimal_places);
                $q->addfield('last_updated',mktime());
                $q->addfield('value',$value);
                $q->run();
                xarResponseRedirect(xarModURL('commerce','admin','currencies'));
                break;
            case 'save':
                if(!xarVarFetch('title','str',$title)) {return;}
                if(!xarVarFetch('code','str',$code)) {return;}
                if(!xarVarFetch('symbol_left','str',$symbol_left)) {return;}
                if(!xarVarFetch('symbol_right','str',  $symbol_right)) {return;}
                if(!xarVarFetch('decimal_point','str',  $decimal_point)) {return;}
                if(!xarVarFetch('thousands_point','str',  $thousands_point)) {return;}
                if(!xarVarFetch('decimal_places','int',  $decimal_places,0,XARVAR_NOT_REQUIRED)) {return;}
                if(!xarVarFetch('value','float',  $value)) {return;}
                $q = new xenQuery('UPDATE', $xartables['commerce_currencies']);
                $q->addfield('title',$title);
                $q->addfield('code',$code);
                $q->addfield('symbol_left',$symbol_left);
                $q->addfield('symbol_right',$symbol_right);
                $q->addfield('decimal_point',$decimal_point);
                $q->addfield('thousands_point',$thousands_point);
                $q->addfield('decimal_places',$decimal_places);
                $q->addfield('last_updated',mktime());
                $q->addfield('value',$value);
                $q->eq('currencies_id',$cID);
                $q->run();
                xarResponseRedirect(xarModURL('commerce','admin','currencies',array('page' => $page,'cID' => $cID)));
            case 'deleteconfirm':
                $q = new xenQuery('DELETE', $xartables['commerce_currencies']);
                $q->eq('currencies_id',$cID);
                $q->run();
                xarResponseRedirect(xarModURL('commerce','admin','currencies',array('page' => $page)));
                break;
            case 'update':
/*                $q = new xenQuery('SELECT', $xartables['commerce_currencies'],array('currencies_id','code', 'title'));
                $q->run();
                while ($currency = $q->output()) {
                  $quote_function = 'quote_' . CURRENCY_SERVER_PRIMARY . '_currency';
                  $rate = $quote_function($currency['code']);
                  if ( (!$rate) && (CURRENCY_SERVER_BACKUP != '') ) {
                    $quote_function = 'quote_' . CURRENCY_SERVER_BACKUP . '_currency';
                    $rate = $quote_function($currency['code']);
                  }
                  if ($rate) {
                    $q = new xenQuery('UPDATE', $xartables['commerce_configuration]);
                    $q->addfield('value',$rate);
                    $q->addfield('last_updated',now());
                    $q->eq('currencies_id',$currency['currencies_id']);
                    $q->run();
                    $messageStack->add_session(sprintf(TEXT_INFO_CURRENCY_UPDATED, $currency['title'], $currency['code']), 'success');
                  } else {
                    $messageStack->add_session(sprintf(ERROR_CURRENCY_INVALID, $currency['title'], $currency['code']), 'error');
                  }
                }
                xarResponseRedirect(xarModURL('commerce','admin','currencies',array('page' => $page ,'cID' => $cID)));
                break;
*/        }
    }

    $localeinfo = xarLocaleGetInfo(xarMLSGetSiteLocale());
    $data['language'] = $localeinfo['lang'] . "_" . $localeinfo['country'];

    $q = new xenQuery('SELECT');
    $q->addtable($xartables['commerce_currencies']);
    $q->addfields(array('currencies_id', 'title', 'code', 'symbol_left', 'symbol_right', 'decimal_point', 'thousands_point', 'decimal_places', 'last_updated', 'value'));
    $q->setorder('title');
    $q->setrowstodo(xarModGetVar('commerce', 'itemsperpage'));
    $q->setstartat(($page - 1) * xarModGetVar('commerce', 'itemsperpage') + 1);
    $q->run();

    $pager = new splitPageResults($page,
                                  $q->getrows(),
                                  xarModURL('commerce','admin','currencies'),
                                  xarModGetVar('commerce', 'itemsperpage')
                                 );
    $data['pagermsg'] = $pager->display_count('Displaying #(1) to #(2) (of #(3) currencies)');
    $data['displaylinks'] = $pager->display_links();

    $items =$q->output();
    $limit = count($items);
    for ($i=0;$i<$limit;$i++) {
        if ((!isset($cID) || $cID == $items[$i]['currencies_id']) && !isset($cInfo) && (substr($action, 0, 3) != 'new')) {
           $cInfo = new objectInfo($items[$i]);
            $items[$i]['url'] = xarModURL('commerce','admin','currencies',array('page' => $page,'cID' => $cInfo->currencies_id, 'action' => 'edit'));
        }
        else {
            $items[$i]['url'] = xarModURL('commerce','admin','currencies',array('page' => $page, 'cID' => $items[$i]['currencies_id']));
        }
    }
    $data['items'] = $items;
    $data['cInfo'] = isset($cInfo) ? get_object_vars($cInfo) : '';
    $data['page'] = $page;
    $data['action'] = $action;

    return $data;

}
?>
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

function commerce_admin_tax_rates()
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
                if(!xarVarFetch('tax_zone_id','int',  $tax_zone_id)) {return;}
                if(!xarVarFetch('tax_class_id','int',  $tax_class_id)) {return;}
                if(!xarVarFetch('tax_rate','float',  $tax_rate)) {return;}
                if(!xarVarFetch('tax_description','str',  $tax_description)) {return;}
                if(!xarVarFetch('tax_priority','int',  $tax_priority)) {return;}
                if(!xarVarFetch('date_added','int ',  $date_added)) {return;}
                $q = new xenQuery('INSERT', $xartables['commerce_tax_rates']);
                $q->addfield('tax_zone_id',$tax_zone_id);
                $q->addfield('tax_class_id',$tax_class_id);
                $q->addfield('tax_rate',$tax_rate);
                $q->addfield('tax_description',$tax_description);
                $q->addfield('tax_priority',$tax_priority);
                $q->addfield('date_added',mktime());
                $q->run();
                xarResponseRedirect(xarModURL('commerce','admin','tax_rates'));
                break;
            case 'save':
                if(!xarVarFetch('tax_zone_id','int',  $tax_zone_id)) {return;}
                if(!xarVarFetch('tax_class_id','int',  $tax_class_id)) {return;}
                if(!xarVarFetch('tax_rate','float',  $tax_rate)) {return;}
                if(!xarVarFetch('tax_description','str',  $tax_description)) {return;}
                if(!xarVarFetch('tax_priority','int',  $tax_priority)) {return;}
                if(!xarVarFetch('last_modified','int ',  $last_modified)) {return;}
                $q = new xenQuery('UPDATE', $xartables['commerce_tax_rates']);
                $q->addfield('tax_zone_id',$tax_zone_id);
                $q->addfield('tax_class_id',$tax_class_id);
                $q->addfield('tax_rate',$tax_rate);
                $q->addfield('tax_description',$tax_description);
                $q->addfield('tax_priority',$tax_priority);
                $q->addfield('last_modified',mktime());
                $q->eq('tax_rates_id',$cID);
                $q->run();
                xarResponseRedirect(xarModURL('commerce','admin','tax_rates',array('page' => $page,'cID' => $cID)));

            case 'deleteconfirm':
                $q = new xenQuery('DELETE', $xartables['commerce_tax_rates']);
                $q->eq('tax_rates_id',$cID);
                $q->run();
                xarResponseRedirect(xarModURL('commerce','admin','tax_rates',array('page' => $page)));
                break;
        }
    }
    $localeinfo = xarLocaleGetInfo(xarMLSGetSiteLocale());
    $data['language'] = $localeinfo['lang'] . "_" . $localeinfo['country'];

    $q = new xenQuery('SELECT');
    $q->addtable($xartables['commerce_geo_zones'],'z');
    $q->addtable($xartables['commerce_tax_class'],'tc');
    $q->addtable($xartables['commerce_tax_rates'],'r');
    $q->addfields(array('r.tax_rates_id', 'z.geo_zone_id', 'z.geo_zone_name', 'tc.tax_class_title', 'tc.tax_class_id', 'r.tax_priority', 'r.tax_rate', 'r.tax_description', 'r.date_added', 'r.last_modified'));
    $q->join('r.tax_zone_id','z.geo_zone_id');
    $q->join('r.tax_class_id','tc.tax_class_id');
    $q->setorder('tax_class_title');
    $q->setrowstodo(xarModGetVar('commerce', 'itemsperpage'));
    $q->setstartat(($page - 1) * xarModGetVar('commerce', 'itemsperpage') + 1);
//    $q->setstatement();
//    echo $q->getstatement();exit;
    $q->run();


    $pager = new splitPageResults($page,
                                  $q->getrows(),
                                  xarModURL('commerce','admin','tax_rates'),
                                  xarModGetVar('commerce', 'itemsperpage')
                                 );
    $data['pagermsg'] = $pager->display_count('Displaying #(1) to #(2) (of #(3) tax rates)');
    $data['displaylinks'] = $pager->display_links();

    $items =$q->output();
    $limit = count($items);
    for ($i=0;$i<$limit;$i++) {
        if ((!isset($cID) || $cID == $items[$i]['tax_rates_id']) && !isset($cInfo) && (substr($action, 0, 3) != 'new')) {
           $cInfo = new objectInfo($items[$i]);
            $items[$i]['url'] = xarModURL('commerce','admin','tax_rates',array('page' => $page,'cID' => $cInfo->tax_rates_id, 'action' => 'edit'));
        }
        else {
            $items[$i]['url'] = xarModURL('commerce','admin','tax_rates',array('page' => $page, 'cID' => $items[$i]['tax_rates_id']));
        }
    }
    $data['items'] = $items;
    $data['cInfo'] = isset($cInfo) ? get_object_vars($cInfo) : '';
    $data['page'] = $page;
    $data['action'] = $action;

    return $data;
}
?>
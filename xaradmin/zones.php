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

function commerce_admin_zones()
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
                if(!xarVarFetch('zone_country_id','str',  $zone_country_id)) {return;}
                if(!xarVarFetch('zone_code','str',  $zone_code)) {return;}
                if(!xarVarFetch('zone_name','str',$zone_name)) {return;}
                $q = new xenQuery('INSERT', $xartables['commerce_zones']);
                $q->addfield('zone_country_id',$zone_country_id);
                $q->addfield('zone_code',$zone_code);
                $q->addfield('zone_name',$zone_name);
                if(!$q->run()) return;
                xarResponseRedirect(xarModURL('commerce','admin','zones'));
                break;
            case 'save':
                if(!xarVarFetch('zone_country_id','str',  $zone_country_id)) {return;}
                if(!xarVarFetch('zone_code','str',  $zone_code)) {return;}
                if(!xarVarFetch('zone_name','str',$zone_name)) {return;}

                $q = new xenQuery('UPDATE', $xartables['commerce_zones']);
                $q->addfield('zone_country_id',$zone_country_id);
                $q->addfield('zone_code',$zone_code);
                $q->addfield('zone_name',$zone_name);
                $q->eq('zone_id',$cID);
                if(!$q->run()) return;
                xarResponseRedirect(xarModURL('commerce','admin','zones',array('page' => $page,'cID' => $cID)));

            case 'deleteconfirm':
                $q = new xenQuery('DELETE', $xartables['commerce_zones']);
                $q->eq('zone_id',$cID);
                if(!$q->run()) return;
                xarResponseRedirect(xarModURL('commerce','admin','zones',array('page' => $page)));
                break;
        }
    }

    $localeinfo = xarLocaleGetInfo(xarMLSGetSiteLocale());
    $data['language'] = $localeinfo['lang'] . "_" . $localeinfo['country'];

    $q = new xenQuery('SELECT');
    $q->addtable($xartables['commerce_zones'],'z');
    $q->addtable($xartables['commerce_countries'],'c');
    $q->addfields(array('z.zone_id','c.countries_id', 'c.countries_name','z.zone_name','z.zone_code','z.zone_country_id'));
    $q->join('z.zone_country_id','c.countries_id');
    $q->setorder('c.countries_name');
    $q->addorder('z.zone_name');
    $q->setrowstodo(xarModGetVar('commerce', 'itemsperpage'));
    $q->setstartat(($page - 1) * xarModGetVar('commerce', 'itemsperpage') + 1);
    if(!$q->run()) return;

    $pager = new splitPageResults($page,
                                  $q->getrows(),
                                  xarModURL('commerce','admin','zones'),
                                  xarModGetVar('commerce', 'itemsperpage')
                                 );
    $data['pagermsg'] = $pager->display_count('Displaying #(1) to #(2) (of #(3) zones)');
    $data['displaylinks'] = $pager->display_links();

    $items =$q->output();
    $limit = count($items);
    for ($i=0;$i<$limit;$i++) {
        if ((!isset($cID) || $cID == $items[$i]['zone_id']) && !isset($cInfo) && (substr($action, 0, 3) != 'new')) {
           $cInfo = new objectInfo($items[$i]);
            $items[$i]['url'] = xarModURL('commerce','admin','zones',array('page' => $page,'cID' => $cInfo->zone_id, 'action' => 'edit'));
        }
        else {
            $items[$i]['url'] = xarModURL('commerce','admin','zones',array('page' => $page, 'cID' => $items[$i]['zone_id']));
        }
    }
    $data['items'] = $items;
    $data['cInfo'] = isset($cInfo) ? get_object_vars($cInfo) : '';
    $data['page'] = $page;
    $data['action'] = $action;

    return $data;
}
?>
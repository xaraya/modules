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

function commerce_admin_geo_zones()
{
    include_once 'modules/xen/xarclasses/xenquery.php';
    include_once 'modules/commerce/xarclasses/object_info.php';
    include_once 'modules/commerce/xarclasses/split_page_results.php';
    $xartables = xarDBGetTables();

    if(!xarVarFetch('action', 'str',  $action, NULL, XARVAR_DONT_SET)) {return;}
    if(!xarVarFetch('subaction', 'str',  $subaction, NULL, XARVAR_DONT_SET)) {return;}
    if(!xarVarFetch('page',   'int',  $page, 1, XARVAR_NOT_REQUIRED)) {return;}
    if(!xarVarFetch('cID',    'int',  $cID, NULL, XARVAR_DONT_SET)) {return;}
    if(!xarVarFetch('sID',    'int',  $sID, NULL, XARVAR_DONT_SET)) {return;}
    if (isset($subaction)) {
        switch ($subaction) {
            case 'insert_sub':
                if(!xarVarFetch('zone_country_id','int',  $zone_country_id)) {return;}
                if(!xarVarFetch('zone_id','int',  $zone_id)) {return;}
                $q = new xenQuery('INSERT', $xartables['commerce_zones_to_geo_zones']);
                $q->addfield('zone_country_id',$zone_country_id);
                $q->addfield('zone_id',$zone_id);
                $q->addfield('date_added',mktime());
                $q->run();
                xarResponseRedirect(xarModURL('commerce','admin','geo_zones'));
                break;
            case 'save_sub':
                if(!xarVarFetch('zone_country_id','int',  $zone_country_id)) {return;}
                if(!xarVarFetch('zone_id','int',  $zone_id)) {return;}
                $q = new xenQuery('UPDATE', $xartables['commerce_zones_to_geo_zones']);
                $q->addfield('zone_country_id',$zone_country_id);
                $q->addfield('zone_id',$zone_id);
                $q->addfield('last_modified',mktime());
                $q->eq('geo_zone_id',$cID);
                $q->run();
                xarResponseRedirect(xarModURL('commerce','admin','geo_zones',array('page' => $page,'cID' => $cID)));

            case 'deleteconfirm_sub':
                $q = new xenQuery('DELETE', $xartables['commerce_zones_to_geo_zones']);
                $q->eq('association_id',$cID);
                $q->run();
                xarResponseRedirect(xarModURL('commerce','admin','geo_zones',array('page' => $page)));
                break;
        }
    }
    if (isset($action)) {
        switch ($action) {
            case 'insert':
                if(!xarVarFetch('geo_zone_name','str',  $geo_zone_name)) {return;}
                if(!xarVarFetch('geo_zone_description','str',  $geo_zone_description)) {return;}
                $q = new xenQuery('INSERT', $xartables['commerce_geo_zones']);
                $q->addfield('geo_zone_name',$geo_zone_name);
                $q->addfield('geo_zone_description',$geo_zone_description);
                $q->addfield('date_added',mktime());
                $q->run();
                xarResponseRedirect(xarModURL('commerce','admin','geo_zones'));
                break;
            case 'save':
                if(!xarVarFetch('geo_zone_name','str',  $geo_zone_name)) {return;}
                if(!xarVarFetch('geo_zone_description','str',  $geo_zone_description)) {return;}
                $q = new xenQuery('UPDATE', $xartables['commerce_geo_zones']);
                $q->addfield('geo_zone_name',$geo_zone_name);
                $q->addfield('geo_zone_description',$geo_zone_description);
                $q->eq('geo_zone_id',$cID);
                $q->run();
                xarResponseRedirect(xarModURL('commerce','admin','geo_zones',array('page' => $page,'cID' => $cID)));

            case 'deleteconfirm':
                $q = new xenQuery('DELETE', $xartables['commerce_geo_zones']);
                $q->eq('geo_zone_id',$cID);
                $q = new xenQuery('DELETE', $xartables['commerce_zones_to_geo_zones']);
                $q->eq('geo_zone_id',$cID);
                $q->run();
                xarResponseRedirect(xarModURL('commerce','admin','geo_zones',array('page' => $page)));
                break;
        }
    }
    $localeinfo = xarLocaleGetInfo(xarMLSGetSiteLocale());
    $data['language'] = $localeinfo['lang'] . "_" . $localeinfo['country'];

    $q = new xenQuery('SELECT');
    $q->addtable($xartables['commerce_zones_to_geo_zones'],'a');
    $q->addtable($xartables['commerce_countries'],'c');
    $q->addtable($xartables['commerce_geo_zones'],'r');
    $q->addfields(array('a.association_id', 'a.zone_country_id', 'c.countries_name', 'a.zone_id', 'a.geo_zone_id', 'a.last_modified', 'a.date_added', 'z.zone_name');
    $q->join('a.zone_country_id','c.countries_id');
    $q->join('a.zone_id','z.zone_id');
    $q->eq('a.geo_zone_id',$cID );
    $q->setorder('association_id');
    $q->setrowstodo(xarModGetVar('commerce', 'itemsperpage'));
    $q->setstartat(($page - 1) * xarModGetVar('commerce', 'itemsperpage') + 1);
//    $q->setstatement();
//    echo $q->getstatement();exit;
    $q->run();


    $pager = new splitPageResults($page,
                                  $q->getrows(),
                                  xarModURL('commerce','admin','geo_zones'),
                                  xarModGetVar('commerce', 'itemsperpage')
                                 );
    $data['pagermsg'] = $pager->display_count('Displaying #(1) to #(2) (of #(3) tax rates)');
    $data['displaylinks'] = $pager->display_links();

    $items =$q->output();
    $limit = count($items);
    for ($i=0;$i<$limit;$i++) {
        if ((!isset($cID) || $cID == $items[$i]['geo_zone_id']) && !isset($cInfo) && (substr($action, 0, 3) != 'new')) {
           $cInfo = new objectInfo($items[$i]);
            $items[$i]['url'] = xarModURL('commerce','admin','geo_zones',array('page' => $page,'cID' => $cInfo->geo_zone_id, 'action' => 'edit'));
        }
        else {
            $items[$i]['url'] = xarModURL('commerce','admin','geo_zones',array('page' => $page, 'cID' => $items[$i]['geo_zone_id']));
        }
    }
    $data['items'] = $items;
    $data['cInfo'] = isset($cInfo) ? get_object_vars($cInfo) : '';
    $data['page'] = $page;
    $data['action'] = $action;

    return $data;
}
?>
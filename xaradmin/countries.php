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

function commerce_admin_countries()
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
                if(!xarVarFetch('countries_name','str',  $countries_name)) {return;}
                if(!xarVarFetch('countries_iso_code_2','str',$countries_iso_code_2)) {return;}
                if(!xarVarFetch('countries_iso_code_3','str',$countries_iso_code_3)) {return;}
                if(!xarVarFetch('address_format_id','int',$address_format_id)) {return;}
                $q = new xenQuery('INSERT', $xartables['commerce_countries']);
                $q->addfield('countries_name',$countries_name);
                $q->addfield('countries_iso_code_2',$countries_iso_code_2);
                $q->addfield('countries_iso_code_3',$countries_iso_code_3);
                $q->addfield('address_format_id',$address_format_id);
                $q->run();
                xarResponseRedirect(xarModURL('commerce','admin','countries'));
                break;
            case 'save':
                if(!xarVarFetch('countries_name','str',  $countries_name)) {return;}
                if(!xarVarFetch('countries_iso_code_2','str',$countries_iso_code_2)) {return;}
                if(!xarVarFetch('countries_iso_code_3','str',$countries_iso_code_3)) {return;}
                if(!xarVarFetch('address_format_id','int',$address_format_id)) {return;}
                $q = new xenQuery('UPDATE', $xartables['commerce_countries']);
                $q->addfield('countries_name',$countries_name);
                $q->addfield('countries_iso_code_2',$countries_iso_code_2);
                $q->addfield('countries_iso_code_3',$countries_iso_code_3);
                $q->addfield('address_format_id',$address_format_id);
                $q->eq('countries_id',$cID);
                $q->run();
                xarResponseRedirect(xarModURL('commerce','admin','countries',array('page' => $page,'cID' => $cID)));

            case 'deleteconfirm':
                $q = new xenQuery('DELETE', $xartables['commerce_countries']);
                $q->eq('countries_id',$cID);
                $q->run();
                xarResponseRedirect(xarModURL('commerce','admin','countries',array('page' => $page)));
                break;
        }
    }

    $localeinfo = xarLocaleGetInfo(xarMLSGetSiteLocale());
    $data['language'] = $localeinfo['lang'] . "_" . $localeinfo['country'];

    $q = new xenQuery('SELECT');
    $q->addtable($xartables['commerce_countries'],'c');
    $q->addfields(array('countries_id', 'countries_name', 'countries_iso_code_2', 'countries_iso_code_3', 'address_format_id'));
    $q->setorder('countries_name');
    $q->setrowstodo(xarModGetVar('commerce', 'itemsperpage'));
    $q->setstartat(($page - 1) * xarModGetVar('commerce', 'itemsperpage') + 1);
    $q->run();

    $pager = new splitPageResults($page,
                                  $q->getrows(),
                                  xarModURL('commerce','admin','countries'),
                                  xarModGetVar('commerce', 'itemsperpage')
                                 );
    $data['pagermsg'] = $pager->display_count('Displaying #(1) to #(2) (of #(3) countries)');
    $data['displaylinks'] = $pager->display_links();

    $items =$q->output();
    $limit = count($items);
    for ($i=0;$i<$limit;$i++) {
        if ((!isset($cID) || $cID == $items[$i]['countries_id']) && !isset($cInfo) && (substr($action, 0, 3) != 'new')) {
           $cInfo = new objectInfo($items[$i]);
            $items[$i]['url'] = xarModURL('commerce','admin','countries',array('page' => $page,'cID' => $cInfo->countries_id, 'action' => 'edit'));
        }
        else {
            $items[$i]['url'] = xarModURL('commerce','admin','countries',array('page' => $page, 'cID' => $items[$i]['countries_id']));
        }
    }
    $data['items'] = $items;
    $data['cInfo'] = isset($cInfo) ? get_object_vars($cInfo) : '';
    $data['page'] = $page;
    $data['action'] = $action;

    return $data;
}
?>
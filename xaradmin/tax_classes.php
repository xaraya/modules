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

function commerce_admin_tax_classes()
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
                if(!xarVarFetch('tax_class_title','str',  $tax_class_title)) {return;}
                if(!xarVarFetch('tax_class_description','str',  $tax_class_description)) {return;}
                $q = new xenQuery('INSERT', $xartables['commerce_tax_class']);
                $q->addfield('tax_class_title',$tax_class_title);
                $q->addfield('tax_class_description',$tax_class_description);
                $q->addfield('date_added',mktime());
                $q->addfield('last_modified',mktime());
                $q->run();
                xarResponseRedirect(xarModURL('commerce','admin','tax_classes'));
                break;
            case 'save':
                if(!xarVarFetch('tax_class_title','str',  $tax_class_title)) {return;}
                if(!xarVarFetch('tax_class_description','str',  $tax_class_description)) {return;}

                $q = new xenQuery('UPDATE', $xartables['commerce_tax_class']);
                $q->addfield('tax_class_title',$tax_class_title);
                $q->addfield('tax_class_description',$tax_class_description);
                $q->addfield('last_modified',mktime());
                $q->eq('tax_class_id',$cID);
                $q->run();
                xarResponseRedirect(xarModURL('commerce','admin','tax_classes',array('page' => $page,'cID' => $cID)));

            case 'deleteconfirm':
                $q = new xenQuery('DELETE', $xartables['commerce_tax_class']);
                $q->eq('tax_class_id',$cID);
                $q->run();
                xarResponseRedirect(xarModURL('commerce','admin','tax_classes',array('page' => $page)));
                break;
        }
    }

    $localeinfo = xarLocaleGetInfo(xarMLSGetSiteLocale());
    $data['language'] = $localeinfo['lang'] . "_" . $localeinfo['country'];

    $q = new xenQuery('SELECT');
    $q->addtable($xartables['commerce_tax_class']);
    $q->addfields(array('tax_class_id', 'tax_class_title', 'tax_class_description', 'last_modified', 'date_added'));
    $q->setorder('tax_class_title');
    $q->setrowstodo(xarModGetVar('commerce', 'itemsperpage'));
    $q->setstartat(($page - 1) * xarModGetVar('commerce', 'itemsperpage') + 1);
    $q->run();

    $pager = new splitPageResults($page,
                                  $q->getrows(),
                                  xarModURL('commerce','admin','tax_classes'),
                                  xarModGetVar('commerce', 'itemsperpage')
                                 );
    $data['pagermsg'] = $pager->display_count('Displaying #(1) to #(2) (of #(3) tax classes)');
    $data['displaylinks'] = $pager->display_links();

    $items =$q->output();
    $limit = count($items);
    for ($i=0;$i<$limit;$i++) {
        if ((!isset($cID) || $cID == $items[$i]['tax_class_id']) && !isset($cInfo) && (substr($action, 0, 3) != 'new')) {
           $cInfo = new objectInfo($items[$i]);
            $items[$i]['url'] = xarModURL('commerce','admin','tax_classes',array('page' => $page,'cID' => $cInfo->tax_class_id, 'action' => 'edit'));
        }
        else {
            $items[$i]['url'] = xarModURL('commerce','admin','tax_classes',array('page' => $page, 'cID' => $items[$i]['tax_class_id']));
        }
    }
    $data['items'] = $items;
    $data['cInfo'] = isset($cInfo) ? get_object_vars($cInfo) : '';
    $data['page'] = $page;
    $data['action'] = $action;

    return $data;
}
?>
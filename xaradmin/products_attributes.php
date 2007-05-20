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

function products_admin_product_attributes()
{
    sys::import('modules.xen.xarclasses.xenquery');
    include_once 'modules/commerce/xarclasses/object_info.php';
    include_once 'modules/commerce/xarclasses/split_page_results.php';
    $xartables = xarDB::getTables();

    $languages = xarModAPIFunc('commerce','user','get_languages');
    $localeinfo = xarLocaleGetInfo(xarMLSGetSiteLocale());
    $data['language'] = $localeinfo['lang'] . "_" . $localeinfo['country'];
    $currentlang = xarModAPIFunc('commerce','user','get_language',array('locale' => $data['language']));

    if(!xarVarFetch('action', 'str',  $action, NULL, XARVAR_DONT_SET)) {return;}
//    if(!xarVarFetch('page',   'int',  $page, 1, XARVAR_NOT_REQUIRED)) {return;}
//    if(!xarVarFetch('cID',    'int',  $cID, NULL, XARVAR_DONT_SET)) {return;}
    if(!xarVarFetch('option_id',   'int',  $option_id, 0, XARVAR_NOT_REQUIRED)) {return;}
    if(!xarVarFetch('value_id',   'int',  $value_id, 0, XARVAR_NOT_REQUIRED)) {return;}
    if(!xarVarFetch('option_page',   'int',  $option_page, 1, XARVAR_NOT_REQUIRED)) {return;}
    if(!xarVarFetch('value_page',   'int',  $value_page, 1, XARVAR_NOT_REQUIRED)) {return;}
    if(!xarVarFetch('attribute_page',   'int',  $attribute_page, 1, XARVAR_NOT_REQUIRED)) {return;}
//                if(!xarVarFetch('option_name','array',$data['option_name'])) {return;}
//                echo var_dump($_POST['option_name']);exit;
//                echo var_dump($data['option_name']);exit;

    if (isset($action)) {
        switch ($action) {
            case 'add_product_options':
                if(!xarVarFetch('product_options_id','id',$product_options_id)) {return;}
                if(!xarVarFetch('option_name','array',$option_name)) {return;}
                for ($i = 0, $n = sizeof($languages); $i < $n; $i ++) {
                    $q = new xenQuery('INSERT',$xartables['product_product_options']);
                    $q->addfield('product_options_id',$product_options_id);
                    $q->addfield('product_options_name',$option_name[$i+1]);
                    $q->addfield('language_id',$languages[$i]['id']);
                    if(!$q->run()) return;
                }
                xarResponseRedirect(xarModURL('products','admin','product_attributes', array('option_page' => $option_page, 'value_page' => $value_page, 'attribute_page' => $attribute_page)));
                break;
            case 'add_product_option_values':
                if(!xarVarFetch('value_name','array',$value_name)) {return;}
                for ($i = 0, $n = sizeof($languages); $i < $n; $i ++) {
                    $q = new xenQuery('INSERT',$xartables['product_product_options_values']);
                    $q->addfield('product_options_values_id',$value_id);
                    $q->addfield('product_options_values_name',$value_name[$languages[$i]['id']]);
                    $q->addfield('language_id',$languages[$i]['id']);
                    if(!$q->run()) return;
                }
                $q = new xenQuery('INSERT',$xartables['product_product_options_values_to_product_options']);
                $q->addfield('product_options_values_id',$value_id);
                $q->addfield('product_options_id',$option_id);
                if(!$q->run()) return;
                xarResponseRedirect(xarModURL('products','admin','product_attributes', array('option_page' => $option_page, 'value_page' => $value_page, 'attribute_page' => $attribute_page)));
                break;
            case 'add_product_attributes':
                $q = new xenQuery('INSERT',$xartables['product_product_attributes']);
                $q->addfield('product_id',$product_id);
                $q->addfield('options_id',$option_id);
                $q->addfield('values_id',$values_id);
                $q->addfield('value_price',$value_price);
                $q->addfield('price_prefix',$price_prefix);
        $product_attributes_id = xtc_db_insert_id();
                if(!$q->run()) return;
                if(!xarVarFetch('product_attributes_filename','str',$product_attributes_filename)) {return;}
                if (($configuration['download_enabled'] == 'true') && $product_attributes_filename != '') {
                    $q = new xenQuery('INSERT',$xartables['product_product_attributes_download']);
                    $q->addfield('product_attributes_id',$product_attributes_id);
                    $q->addfield('product_attributes_filename',$product_attributes_filename);
                    $q->addfield('product_attributes_maxdays',$product_attributes_maxdays);
                    $q->addfield('product_attributes_maxcount',$product_attributes_maxcount);
                    if(!$q->run()) return;
                }
                xarResponseRedirect(xarModURL('products','admin','product_attributes', array('option_page' => $option_page, 'value_page' => $value_page, 'attribute_page' => $attribute_page)));
                break;
            case 'update_option':
                $q = new xenQuery('SELECT');
                $q->addtable($xartables['product_product_options'],'o');
                $q->addtable($xartables['commerce_languages'],'l');
                $q->addfields(array('o.language_id','l.code', 'o.product_options_name'));
                $q->eq('product_options_id',$option_id);
                $q->join('l.languages_id','o.language_id');
                if(!$q->run()) return;
                $data['options_name'] = $q->output();
                break;
            case 'update_option_value':
                $q = new xenQuery('SELECT');
                $q->addtable($xartables['product_product_options_values'],'ov');
                $q->addtable($xartables['commerce_languages'],'l');
                $q->addfields(array('ov.language_id','l.code', 'ov.product_options_values_name'));
                $q->eq('ov.product_options_values_id',$value_id);
                $q->join('l.languages_id','ov.language_id');
                if(!$q->run()) return;
                $data['options_value_name'] = $q->output();
                break;
            case 'update_option_name':
                if(!xarVarFetch('option_name','array',$option_name)) {return;}
                for ($i = 0, $n = sizeof($languages); $i < $n; $i ++) {
                    $q = new xenQuery('UPDATE',$xartables['product_product_options']);
                    $q->addfield('product_options_name',$option_name[$languages[$i]['id']]);
                    $q->eq('product_options_id',$option_id);
                    $q->eq('language_id',$languages[$i]['id']);
                    if(!$q->run()) return;
                }
                xarResponseRedirect(xarModURL('products','admin','product_attributes', array('option_page' => $option_page, 'value_page' => $value_page, 'attribute_page' => $attribute_page)));
                break;
            case 'update_value':
                if(!xarVarFetch('value_name','array',$value_name)) {return;}
                for ($i = 0, $n = sizeof($languages); $i < $n; $i ++) {
                    $q = new xenQuery('UPDATE',$xartables['product_product_options_values']);
                    $q->addfield('product_options_values_name',$value_name[$languages[$i]['id']]);
                    $q->eq('language_id',$languages[$i]['id']);
                    $q->eq('product_options_values_id',$value_id);
                    if(!$q->run()) return;
                }
                $q = new xenQuery('UPDATE',$xartables['product_product_options_values_to_product_options']);
                $q->addfield('product_options_id',$option_id);
                $q->eq('product_options_values_id',$value_id);
                if(!$q->run()) return;
                xarResponseRedirect(xarModURL('products','admin','product_attributes', array('option_page' => $option_page, 'value_page' => $value_page, 'attribute_page' => $attribute_page)));
                break;
            case 'update_product_attribute':
                if(!xarVarFetch('attribute_id','str',$attribute_id)) {return;}
                $q = new xenQuery('UPDATE',$xartables['product_product_attributes']);
                $q->addfield('product_id',$product_id);
                $q->addfield('options_id',$option_id);
                $q->addfield('options_values_id',$values_id);
                $q->addfield('options_values_price',$value_price);
                $q->addfield('price_prefix',$price_prefix);
                $q->eq('product_attributes_id',$attribute_id);
                if (($configuration['download_enabled'] == 'true') && $product_attributes_filename != '') {
                    $q = new xenQuery('UPDATE',$xartables['product_product_attributes_download']);
                    $q->addfield('product_attributes_filename',$product_attributes_filename);
                    $q->addfield('product_attributes_maxdays',$product_attributes_maxdays);
                    $q->addfield('product_attributes_maxcount',$product_attributes_maxcount);
                    $q->eq('product_attributes_id',$attribute_id);
                    if(!$q->run()) return;
                }
                xarResponseRedirect(xarModURL('products','admin','product_attributes', array('option_page' => $option_page, 'value_page' => $value_page, 'attribute_page' => $attribute_page)));
                break;
            case 'delete_option':
                $q = new xenQuery('DELETE', $xartables['product_product_options']);
                $q->eq('product_options_id',$option_id);
                if(!$q->run()) return;
                xarResponseRedirect(xarModURL('products','admin','product_attributes', array('option_page' => $option_page, 'value_page' => $value_page, 'attribute_page' => $attribute_page)));
                break;
            case 'delete_value':
                $q = new xenQuery('DELETE', $xartables['product_product_options_values']);
                $q->eq('product_options_values_id',$value_id);
                if(!$q->run()) return;
                $q = new xenQuery('DELETE', $xartables['product_product_options_values_to_product_options']);
                $q->eq('product_options_values_id',$value_id);
                if(!$q->run()) return;
                xarResponseRedirect(xarModURL('products','admin','product_attributes', array('option_page' => $option_page, 'value_page' => $value_page, 'attribute_page' => $attribute_page)));
                break;
            case 'delete_attribute':
                $q = new xenQuery('DELETE', $xartables['product_product_attributes']);
                $q->eq('product_attributes_id',$attribute_id);
                if(!$q->run()) return;
// Added for DOWNLOAD_ENABLED. Always try to remove attributes, even if downloads are no longer enabled
                $q = new xenQuery('DELETE', $xartables['product_product_attributes_download']);
                $q->eq('product_attributes_id',$attribute_id);
                if(!$q->run()) return;
                xarResponseRedirect(xarModURL('products','admin','product_attributes', array('option_page' => $option_page, 'value_page' => $value_page, 'attribute_page' => $attribute_page)));
                break;
            case 'delete_product_option':
                $q = new xenQuery('SELECT',$xartables['product_product_options']);
                $q->addfields(array('product_options_id', 'product_options_name'));
                $q->eq('product_options_id', $option_id);
                $q->eq('language_id', $currentlang['id']);
                $q->setrowstodo(xarModVars::get('commerce', 'itemsperpage'));
                $q->setstartat(($option_page - 1) * xarModVars::get('commerce', 'itemsperpage') + 1);
                if(!$q->run()) return;
                $data['options_values'] = $q->row();
                break;
            case 'delete_option_value':
                $q = new xenQuery('SELECT',$xartables['product_product_options_values']);
                $q->addfields(array('product_options_values_id', 'product_options_values_name'));
                $q->eq('product_options_values_id', $value_id);
                $q->eq('language_id', $currentlang['id']);
                if(!$q->run()) return;
                $data['delete_value'] = $q->row();
                break;
        }
    }

//    $localeinfo = xarLocaleGetInfo(xarMLSGetSiteLocale());
//    $data['language'] = $localeinfo['lang'] . "_" . $localeinfo['country'];

    $q = new xenQuery('SELECT');
    $q->addtable($xartables['product_products'],'p');
    $q->addtable($xartables['product_product_options_values'],'pov');
    $q->addtable($xartables['product_product_attributes'],'pa');
    $q->addtable($xartables['product_product_description'],'pd');
    $q->addfields(array('p.product_id', 'pd.product_name', 'pov.product_options_values_name'));
    $q->join('pd.product_id','p.product_id');
    $q->join('pa.product_id','p.product_id');
    $q->join('pov.product_options_values_id','pa.options_values_id');
    $q->eq('pov.language_id',$currentlang);
    $q->eq('pd.language_id',$currentlang);
//    $q->eq('pa.options_id',$option_id);
    $q->setorder('pd.product_name');
//    if(!$q->run()) return;
    $data['products'] = $q->output();

    $q = new xenQuery('SELECT');
    $q->addtable($xartables['product_products'],'p');
    $q->addtable($xartables['product_product_options'],'po');
    $q->addtable($xartables['product_product_attributes'],'pa');
    $q->addtable($xartables['product_product_description'],'pd');
    $q->addfields(array('p.product_id', 'pd.product_name', 'po.product_options_name'));
    $q->join('pd.product_id','p.product_id');
    $q->join('pa.product_id','p.product_id');
    $q->join('po.product_options_id','pa.options_id');
    $q->eq('pd.language_id',$currentlang['id']);
    $q->eq('po.language_id',$currentlang['id']);
//    $q->eq('pa.options_values_id',$value_id);
    $q->setorder('pd.product_name');
//    if(!$q->run()) return;
    $data['product_values'] = $q->output();

    if(!xarVarFetch('option_order_by', 'str',  $option_order_by, 'product_options_id', XARVAR_DONT_SET)) {return;}
    $q = new xenQuery('SELECT',$xartables['product_product_options']);
    $q->eq('language_id',$currentlang['id']);
    $q->setorder($option_order_by);
    $q->setrowstodo(xarModVars::get('commerce', 'itemsperpage'));
    $q->setstartat(($option_page - 1) * xarModVars::get('commerce', 'itemsperpage') + 1);
    if(!$q->run()) return;
    $data['option_values'] = $q->output;

    $selection['startnum'] = '%%';
    $data['option_pager'] = xarTplGetPager($q->getstartat(),
                            $q->getrows(),
                            xarModURL('ledger', 'user', 'arcustomerlist',$selection),
                            $q->getrowstodo());

    $q = new xenQuery('SELECT',$xartables['product_product_options']);
    $q->addfield('max(product_options_id) AS next_id');
    if(!$q->run()) return;
    $max_options_id_values = $q->row();
    $data['next_id'] = isset($max_options_id_values['next_id']) ? $max_options_id_values['next_id'] + 1: 1;

    $q = new xenQuery('SELECT',$xartables['product_product_options_values']);
    $q->addfield('max(product_options_values_id) AS next_id');
    if(!$q->run()) return;
    $max_options_id_values = $q->row();
    $data['next_value_id'] = isset($max_options_id_values['next_id']) ? $max_options_id_values['next_id'] + 1: 1;

    if(!xarVarFetch('option_order_by', 'str',  $option_order_by, 'product_options_id', XARVAR_DONT_SET)) {return;}

    $q = new xenQuery('SELECT',$xartables['product_product_options_values']);
    $q->addfields(array('product_options_values_id', 'product_options_values_name'));
    $q->eq('language_id',$currentlang['id']);
    $q->setrowstodo(xarModVars::get('commerce', 'itemsperpage'));
    $q->setstartat(($option_page - 1) * xarModVars::get('commerce', 'itemsperpage') + 1);
//            $q->setstatement();
//            echo $q->getstatement();exit;
    if(!$q->run()) return;
    $data['values_values'] = $q->output;
//    echo var_dump($data['values_values']);exit;

    $q = new xenQuery('SELECT',$xartables['product_product_options']);
    $q->addfields(array('product_options_id','product_options_name'));
    $q->eq('language_id',$currentlang['id']);
    $q->setorder('product_options_name');
    if(!$q->run()) return;
    $data['option_list'] = $q->output();

    $q = new xenQuery('SELECT',$xartables['product_product_options'],'product_options_name');
    $q->eq('product_options_name',$option_id);
    $q->eq('language_id',$currentlang['id']);
    if(!$q->run()) return;
    $option = $q->row();
    if($option == array()) $data['option_name'] = '';
    else $data['option_name'] = $option['product_options_name'];

    $q = new xenQuery('SELECT');
    $q->addtable($xartables['product_products'],'p');
    $q->addtable($xartables['product_product_options'],'po');
    $q->addtable($xartables['product_product_attributes'],'pa');
    $q->addtable($xartables['product_product_description'],'pd');
    $q->addfields(array('p.product_id', 'pd.product_name', 'po.product_options_name'));
    $q->join('pd.product_id','p.product_id');
    $q->join('pa.product_id','p.product_id');
    $q->join('po.product_options_id','pa.options_id');
    $q->eq('pd.language_id',$currentlang['id']);
    $q->eq('po.language_id',$currentlang['id']);
    $q->setorder('pd.product_name');
    if(!$q->run()) return;

    $data['action'] = $action;
    $data['option_page'] = $option_page;
    $data['value_page'] = $value_page;
    $data['attribute_page'] = $attribute_page;
    $data['option_id'] = $option_id;
    $data['value_id'] = $value_id;
    $data['languages'] = $languages;
//    echo var_dump($data['languages']);exit;
    return $data;
}
?>
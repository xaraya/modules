<?php
// ----------------------------------------------------------------------
// Copyright (C) 2004: Marc Lutolf (marcinmilan@xaraya.com)
// Purpose of file:  Configuration functions for commerce
// ----------------------------------------------------------------------
//  based on:
//  (c) 2003-4 Xaraya
//  (c) 2003 XT-Commerce
//   Third Party contributions:
//   Enable_Disable_Categories 1.3            Autor: Mikel Williams | mikel@ladykatcostumes.com
//  (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
//  (c) 2002-2003 osCommerce (oscommerce.sql,v 1.83); www.oscommerce.com
//  (c) 2003  nextcommerce (nextcommerce.sql,v 1.76 2003/08/25); www.nextcommerce.org
// ----------------------------------------------------------------------

/**
 * Initialise the block
 */
function commerce_whats_newblock_init()
{
    return array(
        'content_text' => '',
        'content_type' => 'text',
        'expire' => 0,
        'hide_empty' => true,
        'custom_format' => '',
        'hide_errors' => true,
        'start_date' => '',
        'end_date' => ''
    );
}

/**
 * Get information on the block ($blockinfo array)
 */
function commerce_whats_newblock_info()
{
    return array(
        'text_type' => 'Content',
        'text_type_long' => 'Generic Content Block',
        'module' => 'commerce',
        'func_update' => 'commerce_whats_new_update',
        'allow_multiple' => true,
        'form_content' => false,
        'form_refresh' => false,
        'show_preview' => true,
        'notes' => "content_type can be 'text', 'html', 'php' or 'data'"
    );
}

/**
 * Display function
 * @param $blockinfo array
 * @returns $blockinfo array
 */
function commerce_whats_newblock_display($blockinfo)
{
    // Security Check
    if (!xarSecurityCheck('ViewCommerceBlocks', 0, 'Block', "content:$blockinfo[title]:All")) {return;}

//    $box_content='';

    if ($random_product = xarModAPIFunc('commerce','user','random_select',array('query' =>"select distinct p.products_id, p.products_image, p.products_tax_class_id, p.products_price from " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_TO_CATEGORIES . " p2c, " . TABLE_CATEGORIES . " c where p.products_status=1 and p.products_id = p2c.products_id and p.products_id !='".$_GET['products_id']."' and c.categories_id = p2c.categories_id and c.categories_status=1 order by p.products_date_added desc limit " . MAX_RANDOM_SELECT_NEW))) {
        $whats_new_price = xarModAPIFunc('commerce','user','get_products_price',array('products_id' =>$random_product['products_id'],'price_special' =>$price_special = 1,'quantity' =>$quantity = 1));
    }


    $random_product['products_name']=xarModAPIFunc('commerce','user','get_products_name',array('id' =>$random_product['products_id']));

    if ($random_product['products_name']=='') return;

    $box_content='<a href="' . xarModURL('commerce','user','product_info', 'products_id=' . $random_product['products_id']) . '">' . xtc_image(xarTplGetImage('product_images/thumbnail_images/' . $random_product['products_image']), $random_product['products_name'], PRODUCT_IMAGE_THUMBNAIL_WIDTH, PRODUCT_IMAGE_THUMBNAIL_HEIGHT) . '</a><br><a href="' . xarModURL('commerce','user','product_info', 'products_id=' . $random_product['products_id']) . '">' . $random_product['products_name'] . '</a><br>' . $whats_new_price;

    $image='';
    if ($random_product['products_image']!='') {
    $image= xarTplGetImage('product_images/thumbnail_images/' . $random_product['products_image']);
    }
    $data['LINK'] = xarModURL('commerce','user','product_info', 'products_id=' . $random_product["products_id"]);
    $data['IMAGE'] = $image;
    $data['NAME'] = $random_product['products_name'];
    $data['PRICE'] = xarModAPIFunc('commerce','user','get_products_price',array('products_id' =>$random_product['products_id'],'price_special' =>$price_special=1,'quantity' =>$quantity=1));
    $data['BOX_CONTENT'] = $box_content;

/*          // set cache ID
    if (USE_CACHE=='false') {
    $box_smarty->caching = 0;
    $box_whats_new= $box_smarty->fetch(CURRENT_TEMPLATE.'/boxes/box_whatsnew.html');
    } else {
    $box_smarty->caching = 1;
    $box_smarty->cache_lifetime=CACHE_LIFETIME;
    $box_smarty->cache_modified_check=CACHE_CHECK;
    $cache_id = $_SESSION['language'].$random_product['products_id'].$_SESSION['customers_status']['customers_status_name'];
    $box_whats_new= $box_smarty->fetch(CURRENT_TEMPLATE.'/boxes/box_whatsnew.html',$cache_id);
    }
*/
    $blockinfo['content'] = $data;
    return $blockinfo;
}
?>
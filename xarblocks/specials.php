<?php
// ----------------------------------------------------------------------
// Copyright (C) 2004: Marc Lutolf (marcinmilan@xaraya.com)
// Purpose of file:  Configuration functions for commerce
// ----------------------------------------------------------------------
//  based on:
//  (c) 2003-4 Xaraya
//  (c) 2003 XT-Commerce
//  (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
//  (c) 2002-2003 osCommerce (oscommerce.sql,v 1.83); www.oscommerce.com
//  (c) 2003  nextcommerce (nextcommerce.sql,v 1.76 2003/08/25); www.nextcommerce.org
// ----------------------------------------------------------------------

/**
 * Initialise the block
 */
function commerce_specialsblock_init()
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
function commerce_specialsblock_info()
{
    return array(
        'text_type' => 'Content',
        'text_type_long' => 'Generic Content Block',
        'module' => 'commerce',
        'func_update' => '',
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
function commerce_specialsblock_display($blockinfo)
{
    // Security Check
    if (!xarSecurityCheck('ViewCommerceBlocks', 0, 'Block', "content:$blockinfo[title]:All")) {return;}

    $blockinfo = array();

    //FIXME: create an API function for this stuff
    include_once 'modules/xen/xarclasses/xenquery.php';
    xarModAPILoad('commerce');
    $xartables = xarDBGetTables();

    $selectfields = array('p.product_id', 'pd.product_name', 'p.product_price', 'p.product_tax_class_id', 'p.product_image','s.specials_new_product_price');
    $q = new xenQuery("SELECT",$xartables['commerce_products'],$selectfields);
    $q->setalias($xartables['commerce_products'],'p');
    $q->addtable($xartables['commerce_product_description'],'pd');
    $q->addtable($xartables['commerce_specials'],'s');
    $q->join('p.product_id', 's.product_id');
    $q->join('pd.product_id', 's.product_id');
//FIXME    $q->eq('pd.language_id', $_SESSION['languages_id']);
    $q->eq('pd.language_id', 1);
    $q->eq('p.product_status', 1);
    $q->eq('s.status', 1);
    $q->setorder('s.specials_date_added','DESC');
//FIXME   $q->setrowstodo(MAX_RANDOM_SELECT_SPECIALS);
    if (!$random_product = xarModAPIFunc('commerce','user','random_select',array('query' => $q ))) {
        return '';
    }
    $data['link'] = xarModURL('commerce','user','product_info.php', array('product_id' =>$random_product['product_id']));
    $data['image'] = xarTplGetImage('product_images/thumbnail_images/' . $random_product['product_image']);
    $data['name'] = $random_product['product_name'];
    $data['price'] =xarModAPIFunc('commerce','user','get_product_price',array('product_id' =>$random_product['product_id'],'price_special' =>$price_special=1,'quantity' =>$quantity=1));


//    $box_content='<a href="' . xarModURL('commerce','user','product_info', 'product_id=' . $random_product["product_id"]) . '">' . xtc_image(xarTplGetImage('product_images/thumbnail_images/' . $random_product['product_image']), $random_product['product_name'], PRODUCT_THUMBNAIL_IMAGE_WIDTH, PRODUCT_THUMBNAIL_IMAGE_HEIGHT) . '</a><br><a href="' . xarModURL('commerce','user','product_info', 'product_id=' . $random_product['product_id']) . '">' . $random_product['product_name'] . '</a><br>'.xarModAPIFunc('commerce','user','get_product_price',array('product_id' =>$random_product['product_id'],'price_special' =>$price_special=1,'quantity' =>$quantity=1));

    $data['specials_link'] = xarModURL('commerce','user','specials');


//    $box_smarty->assign('language', $_SESSION['language']);
  if ($random_product["product_id"]=='') return '';
/*          // set cache ID
  if (USE_CACHE=='false') {
  $box_smarty->caching = 0;
  $box_specials= $box_smarty->fetch(CURRENT_TEMPLATE.'/boxes/box_specials.html');
  } else {
  $box_smarty->caching = 1;
  $box_smarty->cache_lifetime=CACHE_LIFETIME;
  $box_smarty->cache_modified_check=CACHE_CHECK;
  $cache_id = $_SESSION['language'].$random_product["product_id"].$_SESSION['customers_status']['customers_status_name'];
  $box_specials= $box_smarty->fetch(CURRENT_TEMPLATE.'/boxes/box_specials.html',$cache_id);
  }
*/
    $blockinfo['content'] = $data;
    return $blockinfo;
}
?>
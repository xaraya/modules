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
function commerce_reviewsblock_init()
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
function commerce_reviewsblock_info()
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
function commerce_reviewsblock_display($blockinfo)
{
    // Security Check
    if (!xarSecurityCheck('ViewCommerceBlocks', 0, 'Block', "content:$blockinfo[title]:All")) {return;}

//$box_content='';
  // include needed functions

    $selectfields = array('r.reviews_id', 'r.reviews_rating', 'p.products_id', 'p.products_image', 'pd.products_name');
    $q = new xenQuery('SELECT',$xartables['commerce_reviews'],$selectfields);
    $q->setalias($xartables['commerce_reviews'],'r');
    $q->addtable($xartables['commerce_reviews_description'],'rd');
    $q->addtable($xartables['commerce_products'],'p');
    $q->addtable($xartables['commerce_products_description'],'pd');
    $q->eq('products_status',1);
    $q->join('p.products_id','r.products_id');
    $q->join('r.reviews_id','rd.reviews_id');
    $q->eq('rd.languages_id',$_SESSION['languages_id']);
    $q->join('p.products_id','pd.products_id');
    $q->eq('pd.languages_id',$_SESSION['languages_id']);

    if(!xarVarFetch('products_id', 'int', $productid, NULL, XARVAR_DONT_SET)) {return;}
    if (isset($productid)) {
        $q->eq('p.products_id',$productid); .= " and  = '" . (int)$_GET['products_id'] . "'";
    }
    $q->storder('r.reviews_id','desc');
    //FIXME  $q->setrowstodo(MAX_RANDOM_SELECT_REVIEWS);
    $random_product = xarModAPIFunc('commerce','user','random_select',array('query' =>$q));

  if ($random_product) {
    // display random review box
    $review_query = new xenQuery("select substring(reviews_text, 1, 60) as reviews_text from " . TABLE_REVIEWS_DESCRIPTION . " where reviews_id = '" . $random_product['reviews_id'] . "' and languages_id = '" . $_SESSION['languages_id'] . "'");
      $q = new xenQuery();
      $q->run();
    $review = $q->output();

    $review = htmlspecialchars($review['reviews_text']);
    $review = xarModAPIFunc('commerce','user','break_string,array('string' => $review,'length' => 15, 'break' => '-<br>');

    $box_content = '<div align="center"><a href="' . xarModURL('commerce','user','product_reviews_info',array('products_id' => $random_product['products_id'], 'reviews_id' => $random_product['reviews_id'])) . '">' . xtc_image(xarTplGetImage('product_images/thumbnail_images/' . $random_product['products_image']), $random_product['products_name'], PRODUCT_IMAGE_THUMBNAIL_WIDTH, PRODUCT_IMAGE_THUMBNAIL_HEIGHT) . '</a></div><a href="' . xarModURL('commerce','user','product_reviews_info',array('products_id' => . $random_product['products_id'],'reviews_id' => $random_product['reviews_id'])) . '">' . $review . ' ..</a><br><div align="center">' . xtc_image(xarTplGetImage(DIR_WS_IMAGES . 'stars_' . $random_product['reviews_rating'] . '.gif' , sprintf(BOX_REVIEWS_TEXT_OF_5_STARS, $random_product['reviews_rating'])) . '</div>';


  } elseif (isset($_GET['products_id'])) {
    // display 'write a review' box
    $box_content = '<table border="0" cellspacing="0" cellpadding="2"><tr><td class="infoBoxContents"><a href="' . xarModURL('commerce','user',(product_reviews_write,array('products_id' => $_GET['products_id'])) . '">' . xtc_image(xarTplGetImage(DIR_WS_IMAGES . 'box_write_review.gif'), IMAGE_BUTTON_WRITE_REVIEW) . '</a></td><td class="infoBoxContents"><a href="' . xarModURL('commerce','user',(product_reviews_info,array('products_id' => $_GET['products_id'])) . '">' . BOX_REVIEWS_WRITE_REVIEW .'</a></td></tr></table>';
   }

  if ($box_content=='') return;
  $box_smarty->assign('REVIEWS_LINK',xarModURL('commerce','user','reviews'));
  $box_smarty->assign('BOX_CONTENT', $box_content);
  $box_smarty->assign('language', $_SESSION['language']);
/*
// set cache ID
  if (USE_CACHE=='false') {
  $box_smarty->caching = 0;
  $box_reviews= $box_smarty->fetch(CURRENT_TEMPLATE.'/boxes/box_reviews.html');
  } else {
  $box_smarty->caching = 1;
  $box_smarty->cache_lifetime=CACHE_LIFETIME;
  $box_smarty->cache_modified_check=CACHE_CHECK;
  $cache_id = $_SESSION['language'].$random_product['reviews_id'];
  $box_reviews= $box_smarty->fetch(CURRENT_TEMPLATE.'/boxes/box_reviews.html',$cache_id);
  }
  */
    $blockinfo['content'] = $data;
    return $blockinfo;
}
?>
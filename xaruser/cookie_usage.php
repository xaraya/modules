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

//    $smarty = new Smarty;
  // include boxes
  require(DIR_WS_INCLUDES.'boxes.php');

  $breadcrumb->add(NAVBAR_TITLE_COOKIE_USAGE, xarModURL('commerce','user','cookie_usage');

 require(DIR_WS_INCLUDES . 'header.php');

  $data['BUTTON_CONTINUE'] = '<a href="' . xarModURL('commerce','user','default') . '">' .
xarModAPIFunc('commerce','user','image',array('src' => xarTplGetImage('buttons/' . xarSession::getVar('language') . '/'.'button_continue.gif'),
        'alt' => IMAGE_BUTTON_CONTINUE);
  . '</a>';
  $data['language'] = $_SESSION['language'];


  // set cache ID
  if (USE_CACHE=='false') {
  $smarty->caching = 0;
  return data;
  } else {
  $smarty->caching = 1;
  $smarty->cache_lifetime=CACHE_LIFETIME;
  $smarty->cache_modified_check=CACHE_CHECK;
  $cache_id = $_SESSION['language'];
  return data;
  }
  ?>
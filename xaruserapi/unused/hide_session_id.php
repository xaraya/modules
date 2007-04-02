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

 // include needed functions
// Hide form elements
  function commerce_userapi_hide_session_id() {
    global $session_started;

    if ( ($session_started == true) && defined('SID') && xarModAPIFunc('commerce','user','not_null',array('arg' =>SID)) ) {
      return xtc_draw_hidden_field(xtc_session_name(), xtc_session_id());
    }
  }
 ?>

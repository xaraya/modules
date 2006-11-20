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

  class objectInfo
  {

    // class constructor
    function objectInfo($object_array)
    {
      reset($object_array);
      while (list($key, $value) = each($object_array)) {
        $this->$key = xarModAPIFunc('commerce','user','db_prepare_input',array('var' => $value));
      }
    }
  }
?>
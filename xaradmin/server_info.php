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

function commerce_admin_server_info()
{


  $system = xtc_get_system_information();
  if (function_exists('ob_start')) {
    ob_start();
    phpinfo();
    $phpinfo = ob_get_contents();
    ob_end_clean();

    $phpinfo = str_replace('border: 1px', '', $phpinfo);
    ereg("(<style type=\"text/css\">{1})(.*)(</style>{1})", $phpinfo, $regs);
    echo '<style type="text/css">' . $regs[2] . '</style>';
    ereg("(<body>{1})(.*)({1})", $phpinfo, $regs);
    echo $regs[2];
  } else {
    phpinfo();
  }
}
?>
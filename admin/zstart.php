<?php
// ----------------------------------------------------------------------
// Xaraya eXtensible Management System
// Copyright (C) 2003 by the Xaraya Development Team.
// http://www.xaraya.org
// ----------------------------------------------------------------------
// Original Author of file: Jim McDonald
// Modified by: Nuncanada
// Modified by: marcinmilan
// Purpose of file:  Initialisation functions for commerce
// ----------------------------------------------------------------------
//  based on:
//  (c) 2003 XT-Commerce
//  (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
//  (c) 2002-2003 osCommerce (oscommerce.sql,v 1.83); www.oscommerce.com
//  (c) 2003  nextcommerce (nextcommerce.sql,v 1.76 2003/08/25); www.nextcommerce.org
// ----------------------------------------------------------------------

  require_once 'includes/classes/carp.php';
?>
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="Content-Type" content="<?php echo $_SESSION['language_charset']; ?>">
<title><?php echo TITLE; ?></title>
<link rel="stylesheet" type="text/css" href="includes/stylesheet.css">
<style type="text/css">
.h2 {
  font-family: Trebuchet MS,Palatino,Times New Roman,serif;
  font-size: 13pt;
  font-weight: bold;
}

.h3 {
  font-family: Verdana,Arial,Helvetica,sans-serif;
  font-size: 9pt;
  font-weight: bold;
}
</style>

</head>
<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF">
<!-- header //-->
<?php require(DIR_WS_INCLUDES . 'header.php'); ?>
<!-- header_eof //-->

<!-- body //-->
<table border="0" width="100%" cellspacing="2" cellpadding="2">
  <tr>
    <td class="columnLeft2" width="<?php echo BOX_WIDTH; ?>" valign="top"><table border="0" width="<?php echo BOX_WIDTH; ?>" cellspacing="1" cellpadding="1" class="columnLeft">
<!-- left_navigation //-->
<?php require(DIR_WS_INCLUDES . 'column_left.php'); ?>
<!-- left_navigation_eof //-->
    </table></td>
<!-- body_text //-->
    <td width="100%" valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="0">
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
  <tr>
    <td width="80" rowspan="2"><?php echo xtc_image(xarTplGetImage(DIR_WS_ICONS.'heading_news.gif'); ?></td>
    <td class="pageHeading"><?php echo HEADING_TITLE; ?></td>
  </tr>
  <tr>
    <td class="main" valign="top">XTC Central</td>
  </tr>
</table></td>
      </tr>
      <tr>
        <td>
        <?php include(DIR_WS_MODULES.FILENAME_SECURITY_CHECK); ?>
        <table valign="top" width="100%" border="0" cellpadding="0" cellspacing="0">

<?php
CarpConf('linkdiv', '<div class="dataTableHeadingRow"">');
CarpConf('linkstyle', 'text-decoration:none');
CarpConf('linkclass', 'newslink');
CarpConf('showdesc|showctitle|showcdesc', 1);
CarpConf('maxitems', 5);
CarpConf('cclass', 'h2');
CarpConf('postitem','');
CarpConf('poweredby','');
CarpShow('http://www.xt-commerce.com/backend.php', 'xt-news.cache');
CarpConfReset();


CarpConf('linkdiv','<div style="background:#cccccc; width:185; padding:2px; border-width:1px; border-style:solid; border-color:#333333;">');
CarpConf('linkstyle','text-decoration:none');
CarpConf('linkclass','h3');
CarpConf('showdesc|showctitle|showcdesc',1);
CarpConf('maxitems',10);
CarpConf('cclass','h2');
CarpConf('postitems','');
CarpConf('poweredby','');
//CarpShow("http://www.xt-commerce.com/modules/xp_syndication/mods/mylinks_rss.php","xt-links.cache");
CarpConfReset();
?>
        </table></td>
      </tr>
    </table></td>
<!-- body_text_eof //-->
  </tr>
</table>
<!-- body_eof //-->

<!-- footer //-->
<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
<!-- footer_eof //-->
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
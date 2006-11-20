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

function commerce_admin_product_attributes()
{

  $languages = xtc_get_languages();

  if ($_GET['action']) {
    $page_info = 'option_page=' . $_GET['option_page'] . '&value_page=' . $_GET['value_page'] . '&attribute_page=' . $_GET['attribute_page'];
    switch($_GET['action']) {
      case 'add_product_options':
        for ($i = 0, $n = sizeof($languages); $i < $n; $i ++) {
          $option_name = $_POST['option_name'];
          new xenQuery("insert into " . TABLE_product_OPTIONS . " (product_options_id, product_options_name, language_id) values ('" . $_POST['product_options_id'] . "', '" . $option_name[$languages[$i]['id']] . "', '" . $languages[$i]['id'] . "')");
        }
        xarRedirectResponse(xarModURL('commerce','admin',(FILENAME_product_ATTRIBUTES, $page_info));
        break;
      case 'add_product_option_values':
        for ($i = 0, $n = sizeof($languages); $i < $n; $i ++) {
          $value_name = $_POST['value_name'];
          new xenQuery("insert into " . TABLE_product_OPTIONS_VALUES . " (product_options_values_id, language_id, product_options_values_name) values ('" . $_POST['value_id'] . "', '" . $languages[$i]['id'] . "', '" . $value_name[$languages[$i]['id']] . "')");
        }
        new xenQuery("insert into " . TABLE_product_OPTIONS_VALUES_TO_product_OPTIONS . " (product_options_id, product_options_values_id) values ('" . $_POST['option_id'] . "', '" . $_POST['value_id'] . "')");
        xarRedirectResponse(xarModURL('commerce','admin',(FILENAME_product_ATTRIBUTES, $page_info));
        break;
      case 'add_product_attributes':
        new xenQuery("insert into " . TABLE_product_ATTRIBUTES . " values ('', '" . $_POST['product_id'] . "', '" . $_POST['options_id'] . "', '" . $_POST['values_id'] . "', '" . $_POST['value_price'] . "', '" . $_POST['price_prefix'] . "')");
        $product_attributes_id = xtc_db_insert_id();
        if ((DOWNLOAD_ENABLED == 'true') && $_POST['product_attributes_filename'] != '') {
          new xenQuery("insert into " . TABLE_product_ATTRIBUTES_DOWNLOAD . " values (" . $product_attributes_id . ", '" . $_POST['product_attributes_filename'] . "', '" . $_POST['product_attributes_maxdays'] . "', '" . $_POST['product_attributes_maxcount'] . "')");
        }
        xarRedirectResponse(xarModURL('commerce','admin',(FILENAME_product_ATTRIBUTES, $page_info));
        break;
      case 'update_option_name':
        for ($i = 0, $n = sizeof($languages); $i < $n; $i ++) {
          $option_name = $_POST['option_name'];
          new xenQuery("update " . TABLE_product_OPTIONS . " set product_options_name = '" . $option_name[$languages[$i]['id']] . "' where product_options_id = '" . $_POST['option_id'] . "' and language_id = '" . $languages[$i]['id'] . "'");
        }
        xarRedirectResponse(xarModURL('commerce','admin',(FILENAME_product_ATTRIBUTES, $page_info));
        break;
      case 'update_value':
       $value_name = $_POST['value_name'];
       for ($i = 0, $n = sizeof($languages); $i < $n; $i ++) {
         new xenQuery("update " . TABLE_product_OPTIONS_VALUES . " set product_options_values_name = '" . $value_name[$languages[$i]['id']] . "' where product_options_values_id = '" . $_POST['value_id'] . "' and language_id = '" . $languages[$i]['id'] . "'");
       }
       new xenQuery("update " . TABLE_product_OPTIONS_VALUES_TO_product_OPTIONS . " set product_options_id = '" . $_POST['option_id'] . "' where product_options_values_id = '" . $_POST['value_id'] . "'");
       xarRedirectResponse(xarModURL('commerce','admin',(FILENAME_product_ATTRIBUTES, $page_info));
       break;
      case 'update_product_attribute':
        new xenQuery("update " . TABLE_product_ATTRIBUTES . " set product_id = '" . $_POST['product_id'] . "', options_id = '" . $_POST['options_id'] . "', options_values_id = '" . $_POST['values_id'] . "', options_values_price = '" . $_POST['value_price'] . "', price_prefix = '" . $_POST['price_prefix'] . "' where product_attributes_id = '" . $_POST['attribute_id'] . "'");
        if ((DOWNLOAD_ENABLED == 'true') && $_POST['product_attributes_filename'] != '') {
          new xenQuery("update " . TABLE_product_ATTRIBUTES_DOWNLOAD . "
                        set product_attributes_filename='" . $_POST['product_attributes_filename'] . "',
                            product_attributes_maxdays='" . $_POST['product_attributes_maxdays'] . "',
                            product_attributes_maxcount='" . $_POST['product_attributes_maxcount'] . "'
                        where product_attributes_id = '" . $_POST['attribute_id'] . "'");
        }
        xarRedirectResponse(xarModURL('commerce','admin',(FILENAME_product_ATTRIBUTES, $page_info));
        break;
      case 'delete_option':
        new xenQuery("delete from " . TABLE_product_OPTIONS . " where product_options_id = '" . $_GET['option_id'] . "'");
        xarRedirectResponse(xarModURL('commerce','admin',(FILENAME_product_ATTRIBUTES, $page_info));
        break;
      case 'delete_value':
        new xenQuery("delete from " . TABLE_product_OPTIONS_VALUES . " where product_options_values_id = '" . $_GET['value_id'] . "'");
        new xenQuery("delete from " . TABLE_product_OPTIONS_VALUES . " where product_options_values_id = '" . $_GET['value_id'] . "'");
        new xenQuery("delete from " . TABLE_product_OPTIONS_VALUES_TO_product_OPTIONS . " where product_options_values_id = '" . $_GET['value_id'] . "'");
        xarRedirectResponse(xarModURL('commerce','admin',(FILENAME_product_ATTRIBUTES, $page_info));
        break;
      case 'delete_attribute':
        new xenQuery("delete from " . TABLE_product_ATTRIBUTES . " where product_attributes_id = '" . $_GET['attribute_id'] . "'");
// Added for DOWNLOAD_ENABLED. Always try to remove attributes, even if downloads are no longer enabled
        new xenQuery("delete from " . TABLE_product_ATTRIBUTES_DOWNLOAD . " where product_attributes_id = '" . $_GET['attribute_id'] . "'");
        xarRedirectResponse(xarModURL('commerce','admin',(FILENAME_product_ATTRIBUTES, $page_info));
        break;
    }
  }

  if ($_GET['action'] == 'delete_product_option') { // delete product option
    $options = new xenQuery("select product_options_id, product_options_name from " . TABLE_product_OPTIONS . " where product_options_id = '" . $_GET['option_id'] . "' and language_id = '" . $_SESSION['languages_id'] . "'");
      $q = new xenQuery();
      if(!$q->run()) return;
    $options_values = $q->output();
?>
              <tr>
                <td class="pageHeading">&#160;<?php echo $options_values['product_options_name']; ?>&#160;</td>
                <td>&#160;<?php echo xtc_image(xarTplGetImage(DIR_WS_IMAGES . 'pixel_trans.gif'), '', '1', '53'); ?>&#160;</td>
              </tr>
              <tr>
                <td><table border="0" width="100%" cellspacing="0" cellpadding="2">
                  <tr>
                    <td colspan="3"><?php echo xtc_black_line(); ?></td>
                  </tr>
<?php
    $products = new xenQuery("select p.product_id, pd.product_name, pov.product_options_values_name from " . TABLE_PRODUCTS . " p, " . TABLE_product_OPTIONS_VALUES . " pov, " . TABLE_product_ATTRIBUTES . " pa, " . TABLE_product_DESCRIPTION . " pd where pd.product_id = p.product_id and pov.language_id = '" . $_SESSION['languages_id'] . "' and pd.language_id = '" . $_SESSION['languages_id'] . "' and pa.product_id = p.product_id and pa.options_id='" . $_GET['option_id'] . "' and pov.product_options_values_id = pa.options_values_id order by pd.product_name");
    if ($products->getrows()) {
?>
                  <tr class="dataTableHeadingRow">
                    <td class="dataTableHeadingContent" align="center">&#160;<?php echo TABLE_HEADING_ID; ?>&#160;</td>
                    <td class="dataTableHeadingContent">&#160;<?php echo TABLE_HEADING_PRODUCT; ?>&#160;</td>
                    <td class="dataTableHeadingContent">&#160;<?php echo TABLE_HEADING_OPT_VALUE; ?>&#160;</td>
                  </tr>
                  <tr>
                    <td colspan="3"><?php echo xtc_black_line(); ?></td>
                  </tr>
<?php
      $q = new xenQuery();
      if(!$q->run()) return;
      while ($product_values = $q->output()) {
        $rows++;
?>
                  <tr class="<?php echo (floor($rows/2) == ($rows/2) ? 'attributes-even' : 'attributes-odd'); ?>">
                    <td align="center" class="smallText">&#160;<?php echo $product_values['product_id']; ?>&#160;</td>
                    <td class="smallText">&#160;<?php echo $product_values['product_name']; ?>&#160;</td>
                    <td class="smallText">&#160;<?php echo $product_values['product_options_values_name']; ?>&#160;</td>
                  </tr>
<?php
      }
?>
                  <tr>
                    <td colspan="3"><?php echo xtc_black_line(); ?></td>
                  </tr>
                  <tr>
                    <td colspan="3" class="main"><br><?php echo TEXT_WARNING_OF_DELETE; ?></td>
                  </tr>
                  <tr>
                    <td align="right" colspan="3" class="main"><br><?php echo '<a href="' . xarModURL('commerce','admin',(FILENAME_product_ATTRIBUTES, '&value_page=' . $_GET['value_page'] . '&attribute_page=' . $attribute_page, 'NONSSL') . '">'; ?><?php echo xarModAPIFunc('commerce','user','image',array('src' => xarTplGetImage('buttons/' . xarSessionGetVar('language') . '/'.'button_cancel.gif'),'alt' => ' cancel '); ?></a>&#160;</td>
                  </tr>
<?php
    } else {
?>
                  <tr>
                    <td class="main" colspan="3"><br><?php echo TEXT_OK_TO_DELETE; ?></td>
                  </tr>
                  <tr>
                    <td class="main" align="right" colspan="3"><br><?php echo '<a href="' . xarModURL('commerce','admin',(FILENAME_product_ATTRIBUTES, 'action=delete_option&option_id=' . $_GET['option_id'], 'NONSSL') . '">'; ?>

xarModAPIFunc('commerce','user','image',array('src' => xarTplGetImage('buttons/' . xarSessionGetVar('language') . '/'.'button_delete.gif'),'alt' => ' delete ')                                                                                        </a>&#160;&#160;&#160;<?php echo '<a href="' . xarModURL('commerce','admin',(FILENAME_product_ATTRIBUTES, '&order_by=' . $order_by . '&page=' . $page, 'NONSSL') . '">'; ?><?php echo xarModAPIFunc('commerce','user','image',array('src' => xarTplGetImage('buttons/' . xarSessionGetVar('language') . '/'.'button_cancel.gif'),'alt' => ' cancel '); ?></a>&#160;</td>
                  </tr>
<?php
    }
?>
                </table></td>
              </tr>
<?php
  } else {
    if ($_GET['option_order_by']) {
      $option_order_by = $_GET['option_order_by'];
    } else {
      $option_order_by = 'product_options_id';
    }
?>
              <tr>
                <td colspan="2" class="pageHeading">&#160;<?php echo HEADING_TITLE_OPT; ?>&#160;</td>
                <td align="right"><br><form name="option_order_by" action="<?php echo FILENAME_product_ATTRIBUTES; ?>"><select name="selected" onChange="go_option()"><option value="product_options_id"<?php if ($option_order_by == 'product_options_id') { echo ' SELECTED'; } ?>><?php echo TEXT_OPTION_ID; ?></option><option value="product_options_name"<?php if ($option_order_by == 'product_options_name') { echo ' SELECTED'; } ?>><?php echo TEXT_OPTION_NAME; ?></option></select></form></td>
              </tr>
              <tr>
                <td colspan="3" class="smallText">
<?php
    $per_page = MAX_ROW_LISTS_OPTIONS;
    $options = "select * from " . TABLE_product_OPTIONS . " where language_id = '" . $_SESSION['languages_id'] . "' order by " . $option_order_by;
    if (!$option_page) {
      $option_page = 1;
    }
    $prev_option_page = $option_page - 1;
    $next_option_page = $option_page + 1;

    $option_query = new xenQuery($options);

    $option_page_start = ($per_page * $option_page) - $per_page;
    $num_rows = $option_query->getrows();

    if ($num_rows <= $per_page) {
      $num_pages = 1;
    } else if (($num_rows % $per_page) == 0) {
      $num_pages = ($num_rows / $per_page);
    } else {
      $num_pages = ($num_rows / $per_page) + 1;
    }
    $num_pages = (int) $num_pages;

    $options = $options . " LIMIT $option_page_start, $per_page";

    // Previous
    if ($prev_option_page)  {
      echo '<a href="' . xarModURL('commerce','admin',(FILENAME_product_ATTRIBUTES, 'option_page=' . $prev_option_page) . '"> &lt;&lt; </a> | ';
    }

    for ($i = 1; $i <= $num_pages; $i++) {
      if ($i != $option_page) {
        echo '<a href="' . xarModURL('commerce','admin',(FILENAME_product_ATTRIBUTES, 'option_page=' . $i) . '">' . $i . '</a> | ';
      } else {
        echo '<b><font color=red>' . $i . '</font></b> | ';
      }
    }

    // Next
    if ($option_page != $num_pages) {
      echo '<a href="' . xarModURL('commerce','admin',(FILENAME_product_ATTRIBUTES, 'option_page=' . $next_option_page) . '"> &gt;&gt; </a>';
    }
?>
                </td>
              </tr>
              <tr>
                <td colspan="3"><?php echo xtc_black_line(); ?></td>
              </tr>
              <tr class="dataTableHeadingRow">
                <td class="dataTableHeadingContent">&#160;<?php echo TABLE_HEADING_ID; ?>&#160;</td>
                <td class="dataTableHeadingContent">&#160;<?php echo TABLE_HEADING_OPT_NAME; ?>&#160;</td>
                <td class="dataTableHeadingContent" align="center">&#160;<?php echo TABLE_HEADING_ACTION; ?>&#160;</td>
              </tr>
              <tr>
                <td colspan="3"><?php echo xtc_black_line(); ?></td>
              </tr>
<?php
    $next_id = 1;
    $options = new xenQuery($options);
      $q = new xenQuery();
      if(!$q->run()) return;
    while ($options_values = $q->output()) {
      $rows++;
?>
              <tr class="<?php echo (floor($rows/2) == ($rows/2) ? 'attributes-even' : 'attributes-odd'); ?>">
<?php
      if (($_GET['action'] == 'update_option') && ($_GET['option_id'] == $options_values['product_options_id'])) {
        echo '<form name="option" action="' . xarModURL('commerce','admin',(FILENAME_product_ATTRIBUTES, 'action=update_option_name', 'NONSSL') . '" method="post">';
        $inputs = '';
        for ($i = 0, $n = sizeof($languages); $i < $n; $i ++) {
          $option_name = new xenQuery("select product_options_name from " . TABLE_product_OPTIONS . " where product_options_id = '" . $options_values['product_options_id'] . "' and language_id = '" . $languages[$i]['id'] . "'");
      $q = new xenQuery();
      if(!$q->run()) return;
          $option_name = $q->output();
          $inputs .= $languages[$i]['code'] . ':&#160;<input type="text" name="option_name[' . $languages[$i]['id'] . ']" size="20" value="' . $option_name['product_options_name'] . '">&#160;<br>';
        }
?>
                <td align="center" class="smallText">&#160;<?php echo $options_values['product_options_id']; ?><input type="hidden" name="option_id" value="<?php echo $options_values['product_options_id']; ?>">&#160;</td>
                <td class="smallText"><?php echo $inputs; ?></td>
                <td align="center" class="smallText">&#160;<?php echo <input type="image" src="#xarTplGetImage('buttons/' . xarSessionGetVar('language') . '/'.'button_update.gif')#" border="0" alt=IMAGE_UPDATE>; ?>&#160;<?php echo '<a href="' . xarModURL('commerce','admin',(FILENAME_product_ATTRIBUTES, '', 'NONSSL') . '">'; ?><?php echo xarModAPIFunc('commerce','user','image',array('src' => xarTplGetImage('buttons/' . xarSessionGetVar('language') . '/'.'button_cancel.gif'),'alt' => IMAGE_CANCEL);; ?></a>&#160;</td>
<?php
        echo '</form>' . "\n";
      } else {
?>
                <td align="center" class="smallText">&#160;<?php echo $options_values["product_options_id"]; ?>&#160;</td>
                <td class="smallText">&#160;<?php echo $options_values["product_options_name"]; ?>&#160;</td>
                <td align="center" class="smallText">&#160;<?php echo '<a href="' . xarModURL('commerce','admin',(FILENAME_product_ATTRIBUTES, 'action=update_option&option_id=' . $options_values['product_options_id'] . '&option_order_by=' . $option_order_by . '&option_page=' . $option_page, 'NONSSL') . '">'; ?>
xarModAPIFunc('commerce','user','image',array('src' => xarTplGetImage('buttons/' . xarSessionGetVar('language') . '/'.'button_edit.gif'),'alt' => IMAGE_UPDATE)
</a>&#160;&#160;<?php echo '<a href="' . xarModURL('commerce','admin',(FILENAME_product_ATTRIBUTES, 'action=delete_product_option&option_id=' . $options_values['product_options_id'], 'NONSSL') , '">'; ?><?php echo xarModAPIFunc('commerce','user','image',array('src' => xarTplGetImage('buttons/' . xarSessionGetVar('language') . '/'.'button_delete.gif'),'alt' => IMAGE_DELETE);; ?></a>&#160;</td>
<?php
      }
?>
              </tr>
<?php
      $max_options_id_query = new xenQuery("select max(product_options_id) + 1 as next_id from " . TABLE_product_OPTIONS);
      $q = new xenQuery();
      if(!$q->run()) return;
      $max_options_id_values = $q->output();
      $next_id = $max_options_id_values['next_id'];
    }
?>
              <tr>
                <td colspan="3"><?php echo xtc_black_line(); ?></td>
              </tr>
<?php
    if ($_GET['action'] != 'update_option') {
?>
              <tr class="<?php echo (floor($rows/2) == ($rows/2) ? 'attributes-even' : 'attributes-odd'); ?>">
<?php
      echo '<form name="options" action="' . xarModURL('commerce','admin',(FILENAME_product_ATTRIBUTES, 'action=add_product_options&option_page=' . $option_page, 'NONSSL') . '" method="post"><input type="hidden" name="product_options_id" value="' . $next_id . '">';
      $inputs = '';
      for ($i = 0, $n = sizeof($languages); $i < $n; $i ++) {
        $inputs .= $languages[$i]['code'] . ':&#160;<input type="text" name="option_name[' . $languages[$i]['id'] . ']" size="20">&#160;<br>';
      }
?>
                <td align="center" class="smallText">&#160;<?php echo $next_id; ?>&#160;</td>
                <td class="smallText"><?php echo $inputs; ?></td>
                <td align="center" class="smallText">&#160;<?php echo <input type="image" src="#xarTplGetImage('buttons/' . xarSessionGetVar('language') . '/'.'button_insert.gif')#" border="0" alt=IMAGE_INSERT>; ?>&#160;</td>
<?php
      echo '</form>';
?>
              </tr>
              <tr>
                <td colspan="3"><?php echo xtc_black_line(); ?></td>
              </tr>
<?php
    }
  }
?>
            </table></td>
<!-- options eof //-->
</tr><tr></tr>
            <td valign="top" width="100%"><table width="100%" border="0" cellspacing="0" cellpadding="2">
<!-- value //-->
<?php
  if ($_GET['action'] == 'delete_option_value') { // delete product option value
    $values = new xenQuery("select product_options_values_id, product_options_values_name from " . TABLE_product_OPTIONS_VALUES . " where product_options_values_id = '" . $_GET['value_id'] . "' and language_id = '" . $_SESSION['languages_id'] . "'");
      $q = new xenQuery();
      if(!$q->run()) return;
    $values_values = $q->output();
?>
              <tr>
                <td colspan="3" class="pageHeading">&#160;<?php echo $values_values['product_options_values_name']; ?>&#160;</td>
                <td>&#160;<?php echo xtc_image(xarTplGetImage(DIR_WS_IMAGES . 'pixel_trans.gif'), '', '1', '53'); ?>&#160;</td>
              </tr>
              <tr>
                <td><table border="0" width="100%" cellspacing="0" cellpadding="2">
                  <tr>
                    <td colspan="3"><?php echo xtc_black_line(); ?></td>
                  </tr>
<?php
    $products = new xenQuery("select p.product_id, pd.product_name, po.product_options_name from " . TABLE_PRODUCTS . " p, " . TABLE_product_ATTRIBUTES . " pa, " . TABLE_product_OPTIONS . " po, " . TABLE_product_DESCRIPTION . " pd where pd.product_id = p.product_id and pd.language_id = '" . $_SESSION['languages_id'] . "' and po.language_id = '" . $_SESSION['languages_id'] . "' and pa.product_id = p.product_id and pa.options_values_id='" . $_GET['value_id'] . "' and po.product_options_id = pa.options_id order by pd.product_name");
    if ($products->getrows()) {
?>
                  <tr class="dataTableHeadingRow">
                    <td class="dataTableHeadingContent" align="center">&#160;<?php echo TABLE_HEADING_ID; ?>&#160;</td>
                    <td class="dataTableHeadingContent">&#160;<?php echo TABLE_HEADING_PRODUCT; ?>&#160;</td>
                    <td class="dataTableHeadingContent">&#160;<?php echo TABLE_HEADING_OPT_NAME; ?>&#160;</td>
                  </tr>
                  <tr>
                    <td colspan="3"><?php echo xtc_black_line(); ?></td>
                  </tr>
<?php
      $q = new xenQuery();
      if(!$q->run()) return;
      while ($product_values = $q->output()) {
        $rows++;
?>
                  <tr class="<?php echo (floor($rows/2) == ($rows/2) ? 'attributes-even' : 'attributes-odd'); ?>">
                    <td align="center" class="smallText">&#160;<?php echo $product_values['product_id']; ?>&#160;</td>
                    <td class="smallText">&#160;<?php echo $product_values['product_name']; ?>&#160;</td>
                    <td class="smallText">&#160;<?php echo $product_values['product_options_name']; ?>&#160;</td>
                  </tr>
<?php
      }
?>
                  <tr>
                    <td colspan="3"><?php echo xtc_black_line(); ?></td>
                  </tr>
                  <tr>
                    <td class="main" colspan="3"><br><?php echo TEXT_WARNING_OF_DELETE; ?></td>
                  </tr>
                  <tr>
                    <td class="main" align="right" colspan="3"><br><?php echo '<a href="' . xarModURL('commerce','admin',(FILENAME_product_ATTRIBUTES, '&value_page=' . $_GET['value_page'] . '&attribute_page=' . $attribute_page, 'NONSSL') . '">'; ?><?php echo xarModAPIFunc('commerce','user','image',array('src' => xarTplGetImage('buttons/' . xarSessionGetVar('language') . '/'.'button_cancel.gif'),'alt' => ' cancel '); ?></a>&#160;</td>
                  </tr>
<?php
    } else {
?>
                  <tr>
                    <td class="main" colspan="3"><br><?php echo TEXT_OK_TO_DELETE; ?></td>
                  </tr>
                  <tr>
                    <td class="main" align="right" colspan="3"><br><?php echo '<a href="' . xarModURL('commerce','admin',(FILENAME_product_ATTRIBUTES, 'action=delete_value&value_id=' . $_GET['value_id'], 'NONSSL') . '">'; ?><?php echo
xarModAPIFunc('commerce','user','image',array('src' => xarTplGetImage('buttons/' . xarSessionGetVar('language') . '/'.'button_delete.gif'),'alt' => ' delete ')
</a>&#160;&#160;&#160;<?php echo '<a href="' . xarModURL('commerce','admin',(FILENAME_product_ATTRIBUTES, '&option_page=' . $option_page . '&value_page=' . $_GET['value_page'] . '&attribute_page=' . $attribute_page, 'NONSSL') . '">'; ?><?php echo xarModAPIFunc('commerce','user','image',array('src' => xarTplGetImage('buttons/' . xarSessionGetVar('language') . '/'.'button_cancel.gif'),'alt' => ' cancel '); ?></a>&#160;</td>
                  </tr>
<?php
    }
?>
                </table></td>
              </tr>
<?php
  } else {
?>
              <tr>
                <td colspan="3" class="pageHeading">&#160;<?php echo HEADING_TITLE_VAL; ?>&#160;</td>
                <td>&#160;<?php echo xtc_image(xarTplGetImage(DIR_WS_IMAGES . 'pixel_trans.gif'), '', '1', '53'); ?>&#160;</td>
              </tr>
              <tr>
                <td colspan="4" class="smallText">
<?php
    $per_page = MAX_ROW_LISTS_OPTIONS;
    $values = "select pov.product_options_values_id, pov.product_options_values_name, pov2po.product_options_id from " . TABLE_product_OPTIONS_VALUES . " pov left join " . TABLE_product_OPTIONS_VALUES_TO_product_OPTIONS . " pov2po on pov.product_options_values_id = pov2po.product_options_values_id where pov.language_id = '" . $_SESSION['languages_id'] . "' order by pov.product_options_values_id";
    if (!$_GET['value_page']) {
      $_GET['value_page'] = 1;
    }
    $prev_value_page = $_GET['value_page'] - 1;
    $next_value_page = $_GET['value_page'] + 1;

    $value_query = new xenQuery($values);

    $value_page_start = ($per_page * $_GET['value_page']) - $per_page;
    $num_rows = $value_query->getrows());

    if ($num_rows <= $per_page) {
      $num_pages = 1;
    } else if (($num_rows % $per_page) == 0) {
      $num_pages = ($num_rows / $per_page);
    } else {
      $num_pages = ($num_rows / $per_page) + 1;
    }
    $num_pages = (int) $num_pages;

    $values = $values . " LIMIT $value_page_start, $per_page";

    // Previous
    if ($prev_value_page)  {
      echo '<a href="' . xarModURL('commerce','admin',(FILENAME_product_ATTRIBUTES, 'option_order_by=' . $option_order_by . '&value_page=' . $prev_value_page) . '"> &lt;&lt; </a> | ';
    }

    for ($i = 1; $i <= $num_pages; $i++) {
      if ($i != $_GET['value_page']) {
         echo '<a href="' . xarModURL('commerce','admin',(FILENAME_product_ATTRIBUTES, 'option_order_by=' . $option_order_by . '&value_page=' . $i) . '">' . $i . '</a> | ';
      } else {
         echo '<b><font color=red>' . $i . '</font></b> | ';
      }
    }

    // Next
    if ($_GET['value_page'] != $num_pages) {
      echo '<a href="' . xarModURL('commerce','admin',(FILENAME_product_ATTRIBUTES, 'option_order_by=' . $option_order_by . '&value_page=' . $next_value_page) . '"> &gt;&gt;</a> ';
    }
?>
                </td>
              </tr>
              <tr>
                <td colspan="4"><?php echo xtc_black_line(); ?></td>
              </tr>
              <tr class="dataTableHeadingRow">
                <td class="dataTableHeadingContent">&#160;<?php echo TABLE_HEADING_ID; ?>&#160;</td>
                <td class="dataTableHeadingContent">&#160;<?php echo TABLE_HEADING_OPT_NAME; ?>&#160;</td>
                <td class="dataTableHeadingContent">&#160;<?php echo TABLE_HEADING_OPT_VALUE; ?>&#160;</td>
                <td class="dataTableHeadingContent" align="center">&#160;<?php echo TABLE_HEADING_ACTION; ?>&#160;</td>
              </tr>
              <tr>
                <td colspan="4"><?php echo xtc_black_line(); ?></td>
              </tr>
<?php
    $next_id = 1;
    $values = new xenQuery($values);
      $q = new xenQuery();
      if(!$q->run()) return;
    while ($values_values = $q->output()) {
      $options_name = xtc_options_name($values_values['product_options_id']);
      $values_name = $values_values['product_options_values_name'];
      $rows++;
?>
              <tr class="<?php echo (floor($rows/2) == ($rows/2) ? 'attributes-even' : 'attributes-odd'); ?>">
<?php
      if (($_GET['action'] == 'update_option_value') && ($_GET['value_id'] == $values_values['product_options_values_id'])) {
        echo '<form name="values" action="' . xarModURL('commerce','admin',(FILENAME_product_ATTRIBUTES, 'action=update_value', 'NONSSL') . '" method="post">';
        $inputs = '';
        for ($i = 0, $n = sizeof($languages); $i < $n; $i ++) {
          $value_name = new xenQuery("select product_options_values_name from " . TABLE_product_OPTIONS_VALUES . " where product_options_values_id = '" . $values_values['product_options_values_id'] . "' and language_id = '" . $languages[$i]['id'] . "'");
      $q = new xenQuery();
      if(!$q->run()) return;
          $value_name = $q->output();
          $inputs .= $languages[$i]['code'] . ':&#160;<input type="text" name="value_name[' . $languages[$i]['id'] . ']" size="15" value="' . $value_name['product_options_values_name'] . '">&#160;<br>';
        }
?>
                <td align="center" class="smallText">&#160;<?php echo $values_values['product_options_values_id']; ?><input type="hidden" name="value_id" value="<?php echo $values_values['product_options_values_id']; ?>">&#160;</td>
                <td align="center" class="smallText">&#160;<?php echo "\n"; ?><select name="option_id">
<?php
        $options = new xenQuery("select product_options_id, product_options_name from " . TABLE_product_OPTIONS . " where language_id = '" . $_SESSION['languages_id'] . "' order by product_options_name");
      $q = new xenQuery();
      if(!$q->run()) return;
        while ($options_values = $q->output()) {
          echo "\n" . '<option name="' . $options_values['product_options_name'] . '" value="' . $options_values['product_options_id'] . '"';
          if ($values_values['product_options_id'] == $options_values['product_options_id']) {
            echo ' selected';
          }
          echo '>' . $options_values['product_options_name'] . '</option>';
        }
?>
                </select>&#160;</td>
                <td class="smallText"><?php echo $inputs; ?></td>
                <td align="center" class="smallText">&#160;<?php echo <input type="image" src="#xarTplGetImage('buttons/' . xarSessionGetVar('language') . '/'.'button_update.gif')#" border="0" alt=IMAGE_UPDATE>; ?>&#160;<?php echo '<a href="' . xarModURL('commerce','admin',(FILENAME_product_ATTRIBUTES, '', 'NONSSL') . '">'; ?><?php echo xarModAPIFunc('commerce','user','image',array('src' => xarTplGetImage('buttons/' . xarSessionGetVar('language') . '/'.'button_cancel.gif'),'alt' => IMAGE_CANCEL);; ?></a>&#160;</td>
<?php
        echo '</form>';
      } else {
?>
                <td align="center" class="smallText">&#160;<?php echo $values_values["product_options_values_id"]; ?>&#160;</td>
                <td align="center" class="smallText">&#160;<?php echo $options_name; ?>&#160;</td>
                <td class="smallText">&#160;<?php echo $values_name; ?>&#160;</td>
                <td align="center" class="smallText">&#160;<?php echo '<a href="' . xarModURL('commerce','admin',(FILENAME_product_ATTRIBUTES, 'action=update_option_value&value_id=' . $values_values['product_options_values_id'] . '&value_page=' . $_GET['value_page'], 'NONSSL') . '">'; ?>
xarModAPIFunc('commerce','user','image',array('src' => xarTplGetImage('buttons/' . xarSessionGetVar('language') . '/'.'button_edit.gif'),'alt' => IMAGE_UPDATE)
</a>&#160;&#160;<?php echo '<a href="' . xarModURL('commerce','admin',(FILENAME_product_ATTRIBUTES, 'action=delete_option_value&value_id=' . $values_values['product_options_values_id'], 'NONSSL') , '">'; ?><?php echo xarModAPIFunc('commerce','user','image',array('src' => xarTplGetImage('buttons/' . xarSessionGetVar('language') . '/'.'button_delete.gif'),'alt' => IMAGE_DELETE);; ?></a>&#160;</td>
<?php
      }
      $max_values_id_query = new xenQuery("select max(product_options_values_id) + 1 as next_id from " . TABLE_product_OPTIONS_VALUES);
      $q = new xenQuery();
      if(!$q->run()) return;
      $max_values_id_values = $q->output();
      $next_id = $max_values_id_values['next_id'];
    }
?>
              </tr>
              <tr>
                <td colspan="4"><?php echo xtc_black_line(); ?></td>
              </tr>
<?php
    if ($_GET['action'] != 'update_option_value') {
?>
              <tr class="<?php echo (floor($rows/2) == ($rows/2) ? 'attributes-even' : 'attributes-odd'); ?>">
<?php
      echo '<form name="values" action="' . xarModURL('commerce','admin',(FILENAME_product_ATTRIBUTES, 'action=add_product_option_values&value_page=' . $_GET['value_page'], 'NONSSL') . '" method="post">';
?>
                <td align="center" class="smallText">&#160;<?php echo $next_id; ?>&#160;</td>
                <td align="center" class="smallText">&#160;<select name="option_id">
<?php
      $options = new xenQuery("select product_options_id, product_options_name from " . TABLE_product_OPTIONS . " where language_id = '" . $_SESSION['languages_id'] . "' order by product_options_name");
      $q = new xenQuery();
      if(!$q->run()) return;
      while ($options_values = $q->output()) {
        echo '<option name="' . $options_values['product_options_name'] . '" value="' . $options_values['product_options_id'] . '">' . $options_values['product_options_name'] . '</option>';
      }

      $inputs = '';
      for ($i = 0, $n = sizeof($languages); $i < $n; $i ++) {
        $inputs .= $languages[$i]['code'] . ':&#160;<input type="text" name="value_name[' . $languages[$i]['id'] . ']" size="15">&#160;<br>';
      }
?>
                </select>&#160;</td>
                <td class="smallText"><input type="hidden" name="value_id" value="<?php echo $next_id; ?>"><?php echo $inputs; ?></td>
                <td align="center" class="smallText">&#160;<?php echo <input type="image" src="#xarTplGetImage('buttons/' . xarSessionGetVar('language') . '/'.'button_insert.gif')#" border="0" alt=IMAGE_INSERT>; ?>&#160;</td>
<?php
      echo '</form>';
?>
              </tr>
              <tr>
                <td colspan="4"><?php echo xtc_black_line(); ?></td>
              </tr>
<?php
    }
  }
}
?>
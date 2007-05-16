<?php
/* --------------------------------------------------------------
   $Id: categories_view.php,v 1.3 2003/12/29 18:42:43 fanta2k Exp $

   XT-Commerce - community made shopping
   http://www.xt-commerce.com

   Copyright (c) 2003 XT-Commerce
   --------------------------------------------------------------
   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(categories.php,v 1.140 2003/03/24); www.oscommerce.com
   (c) 2003  nextcommerce (categories.php,v 1.37 2003/08/18); www.nextcommerce.org

   Released under the GNU General Public License
   --------------------------------------------------------------
   Third Party contribution:
   Enable_Disable_Categories 1.3               Autor: Mikel Williams | mikel@ladykatcostumes.com
   New Attribute Manager v4b                   Autor: Mike G | mp3man@internetwork.net | http://downloads.ephing.com
   Category Descriptions (Version: 1.5 MS2)    Original Author:   Brian Lowe <blowe@wpcusrgrp.org> | Editor: Lord Illicious <shaolin-venoms@illicious.net>
   Customers Status v3.x  (c) 2002-2003 Copyright Elari elari@free.fr | www.unlockgsm.com/dload-osc/ | CVS : http://cvs.sourceforge.net/cgi-bin/viewcvs.cgi/elari/?sortby=date#dirlist

   Released under the GNU General Public License
   --------------------------------------------------------------*/
?>
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td class="pageHeading"><?php echo HEADING_TITLE; ?></td>
            <td class="pageHeading" align="right"><?php echo xtc_draw_separator('pixel_trans.gif', 1, HEADING_IMAGE_HEIGHT); ?></td>
            <td align="right"><table border="0" width="100%" cellspacing="0" cellpadding="0">
              <tr><?php echo xtc_draw_form('search', FILENAME_CATEGORIES, '', 'get'); ?>
                <td class="smallText" align="right"><?php echo HEADING_TITLE_SEARCH . ' ' . xtc_draw_input_field('search', $_GET['search']); ?></td>
              </form></tr>
              <tr><?php echo xtc_draw_form('goto', FILENAME_CATEGORIES, '', 'get'); ?>
                <td class="smallText" align="right"><?php echo HEADING_TITLE_GOTO . ' ' . commerce_userapi_draw_pull_down_menu('cPath', xtc_get_category_tree(), $current_category_id, 'onChange="this.form.submit();"'); ?></td>
              </form></tr>
            </table></td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="2">
              <tr class="dataTableHeadingRow">
                <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_CATEGORIES_PRODUCTS; ?></td>
<?php
    // check Produkt and attributes stock
    if ($_GET['cPath'] != '') {
      echo '<td class="dataTableHeadingContent">' . TABLE_HEADING_STOCK . '</td>';
    }
?>

                <td class="dataTableHeadingContent" align="center"><?php echo TABLE_HEADING_STATUS; ?></td>
                <td class="dataTableHeadingContent" align="center"><?php echo TABLE_HEADING_PRICE; ?></td>
                <td class="dataTableHeadingContent" align="center"><?php echo '% max'; ?></td>
                <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_ACTION; ?>&#160;</td>
              </tr>
<?php
    $categories_count = 0;
    $rows = 0;
    if ($_GET['search']) {
      $categories_query = new xenQuery("select c.categories_id, cd.categories_name, c.categories_image, c.parent_id, c.sort_order, c.date_added, c.last_modified, c.categories_status from " . TABLE_CATEGORIES . " c, " . TABLE_CATEGORIES_DESCRIPTION . " cd where c.categories_id = cd.categories_id and cd.language_id = '" . $_SESSION['languages_id'] . "' and cd.categories_name like '%" . $_GET['search'] . "%' order by c.sort_order, cd.categories_name");
    } else {
      $categories_query = new xenQuery("select c.categories_id, cd.categories_name, c.categories_image, c.parent_id, c.sort_order, c.date_added, c.last_modified, c.categories_status from " . TABLE_CATEGORIES . " c, " . TABLE_CATEGORIES_DESCRIPTION . " cd where c.parent_id = '" . $current_category_id . "' and c.categories_id = cd.categories_id and cd.language_id = '" . $_SESSION['languages_id'] . "' order by c.sort_order, cd.categories_name");
    }
      $q = new xenQuery();
      if(!$q->run()) return;
    while ($categories = $q->output()) {
      $categories_count++;
      $rows++;

      // Get parent_id for subcategories if search
      if ($_GET['search']) $cPath= $categories['parent_id'];

        if ( ((!$_GET['cID']) && (!$_GET['pID']) || (@$_GET['cID'] == $categories['categories_id'])) && (!$cInfo) && (substr($_GET['action'], 0, 4) != 'new_') ) {
        $category_childs = array('childs_count' => xtc_childs_in_category_count($categories['categories_id']));
        $category_products = array('products_count' => xtc_products_in_category_count($categories['categories_id']));
        $cInfo_array = xtc_array_merge($categories, $category_childs, $category_products);
        $cInfo = new objectInfo($cInfo_array);
      }

      if ( (is_object($cInfo)) && ($categories['categories_id'] == $cInfo->categories_id) ) {

      echo '              <tr class="dataTableRowSelected" onmouseover="this.style.cursor=\'hand\'" onclick="document.location.href=\'' . xarModURL('commerce','admin',(FILENAME_CATEGORIES, xtc_get_path($categories['categories_id'])) . '\'">' . "\n";
      } else {
      echo '              <tr class="dataTableRow" onmouseover="this.className=\'dataTableRowOver\';this.style.cursor=\'hand\'" onmouseout="this.className=\'dataTableRow\'" onclick="document.location.href=\'' . xarModURL('commerce','admin',(FILENAME_CATEGORIES, 'cPath=' . $cPath . '&cID=' . $categories['categories_id']) . '\'">' . "\n";

      }
?>
                <td class="dataTableContent"><?php echo '<a href="' . xarModURL('commerce','admin',(FILENAME_CATEGORIES, xtc_get_path($categories['categories_id'])) . '">' . xtc_image(xarTplGetImage('icons/folder.gif'), ICON_FOLDER) . '<a>&#160;<b><a href="'.xarModURL('commerce','admin',(FILENAME_CATEGORIES, 'cPath=' . $cPath . '&cID=' . $categories['categories_id']) .'">' . $categories['categories_name'] . '</a></b>'; ?></td>
                <td></td>
                <td class="dataTableContent" align="center"><?php
      if ($categories['categories_status'] == '1') {
        echo xtc_image(xarTplGetImage(DIR_WS_IMAGES . 'icon_status_green.gif'), IMAGE_ICON_STATUS_GREEN, 10, 10) . '&#160;&#160;<a href="' . xarModURL('commerce','admin',(FILENAME_CATEGORIES, 'action=setflag&flag=0&cID=' . $categories['categories_id'] . '&cPath=' . $cPath) . '">' . xtc_image(xarTplGetImage(DIR_WS_IMAGES . 'icon_status_red_light.gif'), IMAGE_ICON_STATUS_RED_LIGHT, 10, 10) . '</a>';
      } else {
        echo '<a href="' . xarModURL('commerce','admin',(FILENAME_CATEGORIES, 'action=setflag&flag=1&cID=' . $categories['categories_id'] . '&cPath=' . $cPath) . '">' . xtc_image(xarTplGetImage(DIR_WS_IMAGES . 'icon_status_green_light.gif', IMAGE_ICON_STATUS_GREEN_LIGHT, 10, 10) . '</a>&#160;&#160;' . xtc_image(xarTplGetImage(DIR_WS_IMAGES . 'icon_status_red.gif'), IMAGE_ICON_STATUS_RED, 10, 10);
      }
?></td>         <td class="dataTableContent" align="center">&#160;</td>
                <td class="dataTableContent" align="center">&#160;</td>



                <td class="dataTableContent" align="right"><?php if ( (is_object($cInfo)) && ($categories['categories_id'] == $cInfo->categories_id) ) { echo xtc_image(xarTplGetImage(DIR_WS_IMAGES . 'icon_arrow_right.gif', ''); } else { echo '<a href="' . xarModURL('commerce','admin',(FILENAME_CATEGORIES, 'cPath=' . $cPath . '&cID=' . $categories['categories_id']) . '">' . xtc_image(xarTplGetImage(DIR_WS_IMAGES . 'icon_info.gif'), IMAGE_ICON_INFO) . '</a>'; } ?>&#160;</td>
                <td class="dataTableContent" align="center">&#160;</td>

              </tr>
<?php
    }

    $products_count = 0;
    if ($_GET['search']) {
      $products_query = new xenQuery("select p.products_tax_class_id, p.products_id, pd.products_name, p.products_quantity, p.products_image, p.products_price, p.products_discount_allowed, p.products_date_added, p.products_last_modified, p.products_date_available, p.products_status, p2c.categories_id from " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_DESCRIPTION . " pd, " . TABLE_PRODUCTS_TO_CATEGORIES . " p2c where p.products_id = pd.products_id and pd.language_id = '" . $_SESSION['languages_id'] . "' and p.products_id = p2c.products_id and pd.products_name like '%" . $_GET['search'] . "%' order by pd.products_name");
    } else {
      $products_query = new xenQuery("select p.products_tax_class_id, p.products_id, pd.products_name, p.products_quantity, p.products_image, p.products_price, p.products_discount_allowed, p.products_date_added, p.products_last_modified, p.products_date_available, p.products_status from " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_DESCRIPTION . " pd, " . TABLE_PRODUCTS_TO_CATEGORIES . " p2c where p.products_id = pd.products_id and pd.language_id = '" . $_SESSION['languages_id'] . "' and p.products_id = p2c.products_id and p2c.categories_id = '" . $current_category_id . "' order by pd.products_name");
    }
      $q = new xenQuery();
      if(!$q->run()) return;
    while ($products = $q->output()) {
      $products_count++;
      $rows++;

      // Get categories_id for product if search
      if ($_GET['search']) $cPath=$products['categories_id'];

      if ( ((!$_GET['pID']) && (!$_GET['cID']) || (@$_GET['pID'] == $products['products_id'])) && (!$pInfo) && (!$cInfo) && (substr($_GET['action'], 0, 4) != 'new_') ) {
        // find out the rating average from customer reviews
        $reviews_query = new xenQuery("select (avg(reviews_rating) / 5 * 100) as average_rating from " . TABLE_REVIEWS . " where products_id = '" . $products['products_id'] . "'");
      $q = new xenQuery();
      if(!$q->run()) return;
        $reviews = $q->output();
        $pInfo_array = xtc_array_merge($products, $reviews);
        $pInfo = new objectInfo($pInfo_array);
      }

      if ( (is_object($pInfo)) && ($products['products_id'] == $pInfo->products_id) ) {
        echo '              <tr class="dataTableRowSelected" onmouseover="this.style.cursor=\'hand\'" >' . "\n";
      } else {
        echo '              <tr class="dataTableRow" onmouseover="this.className=\'dataTableRowOver\';this.style.cursor=\'hand\'" onmouseout="this.className=\'dataTableRow\'" >' . "\n";
      }
?>
                <td class="dataTableContent"><?php echo '<a href="' . xarModURL('commerce','admin',(FILENAME_CATEGORIES, 'cPath=' . $cPath . '&pID=' . $products['products_id'] ) . '">' . xtc_image(xarTplGetImage('icons/preview.gif'), ICON_PREVIEW) . '&#160;</a><a href="'.xarModURL('commerce','admin',(FILENAME_CATEGORIES, 'cPath=' . $cPath . '&pID=' . $products['products_id']) .'">' . $products['products_name']; ?></a></td>
<?php
      // check Produkt and attributes stock
      if ($_GET['cPath'] != '') {
        echo check_stock($products['products_id']);
      }
?>
                <td class="dataTableContent" align="center"><?php
      if ($products['products_status'] == '1') {
        echo xtc_image(xarTplGetImage(DIR_WS_IMAGES . 'icon_status_green.gif'), IMAGE_ICON_STATUS_GREEN, 10, 10) . '&#160;&#160;<a href="' . xarModURL('commerce','admin',(FILENAME_CATEGORIES, 'action=setflag&flag=0&pID=' . $products['products_id'] . '&cPath=' . $cPath) . '">' . xtc_image(xarTplGetImage(DIR_WS_IMAGES . 'icon_status_red_light.gif'), IMAGE_ICON_STATUS_RED_LIGHT, 10, 10) . '</a>';
      } else {
        echo '<a href="' . xarModURL('commerce','admin',(FILENAME_CATEGORIES, 'action=setflag&flag=1&pID=' . $products['products_id'] . '&cPath=' . $cPath) . '">' . xtc_image(xarTplGetImage(DIR_WS_IMAGES . 'icon_status_green_light.gif'), IMAGE_ICON_STATUS_GREEN_LIGHT, 10, 10) . '</a>&#160;&#160;' . xtc_image(xarTplGetImage(DIR_WS_IMAGES . 'icon_status_red.gif'), IMAGE_ICON_STATUS_RED, 10, 10);
      }
?></td>
<td class="dataTableContent" align="center">
<?php
// Show price
     echo $currencies->format($products['products_price']);
//End Show price
?></td>
               <td class="dataTableContent" align="center">
<?php
     // Show Max Allowed discount
     echo $products['products_discount_allowed'] . '%';
     //  End Show Max Allowed discount
?></td>
                <td class="dataTableContent" align="right"><?php if ( (is_object($pInfo)) && ($products['products_id'] == $pInfo->products_id) ) { echo xtc_image(xarTplGetImage(DIR_WS_IMAGES . 'icon_arrow_right.gif'), ''); } else { echo '<a href="' . xarModURL('commerce','admin',(FILENAME_CATEGORIES, 'cPath=' . $cPath . '&pID=' . $products['products_id']) . '">' . xtc_image(xarTplGetImage(DIR_WS_IMAGES . 'icon_info.gif'), IMAGE_ICON_INFO) . '</a>'; } ?>&#160;</td>
              </tr>
<?php
    }
    if ($cPath_array) {
      $cPath_back = '';
      for($i = 0, $n = sizeof($cPath_array) - 1; $i < $n; $i++) {
        if ($cPath_back == '') {
          $cPath_back .= $cPath_array[$i];
        } else {
          $cPath_back .= '_' . $cPath_array[$i];
        }
      }
    }

    $cPath_back = ($cPath_back) ? 'cPath=' . $cPath_back : '';
?>
              <tr>
                <td colspan="3"><table border="0" width="100%" cellspacing="0" cellpadding="2">
                  <tr>
                    <td class="smallText"><?php echo TEXT_CATEGORIES . '&#160;' . $categories_count . '<br>' . TEXT_PRODUCTS . '&#160;' . $products_count; ?></td>
                    <td align="right" class="smallText"><?php if ($cPath) echo '<a href="' . xarModURL('commerce','admin',(FILENAME_CATEGORIES, $cPath_back . '&cID=' . $current_category_id) . '">' . xarModAPIFunc('commerce','user','image',array('src' => xarTplGetImage('buttons/' . xarSession::getVar('language') . '/'.'button_back.gif'),'alt' => IMAGE_NEW_BACK); . '</a>&#160;'; if (!$_GET['search']) echo '<a href="' . xarModURL('commerce','admin',(FILENAME_CATEGORIES, 'cPath=' . $cPath . '&action=new_category') . '">' .
         xarModAPIFunc('commerce','user','image',array('src' => xarTplGetImage('buttons/' . xarSession::getVar('language') . '/'.'button_new_category.gif'),'alt' => IMAGE_NEW_CATEGORY);
                    </a>&#160;<a href="' . xarModURL('commerce','admin',(FILENAME_CATEGORIES, 'cPath=' . $cPath . '&action=new_product') . '">' . xarModAPIFunc('commerce','user','image',array('src' => xarTplGetImage('buttons/' . xarSession::getVar('language') . '/'.'button_new_product.gif'),'alt' => IMAGE_NEW_PRODUCT); . '</a>'; ?>&#160;</td>
                  </tr>
                </table></td>
              </tr>
            </table></td>
<?php
    $heading = array();
    $contents = array();
    switch ($_GET['action']) {
      case 'new_category':
        $heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_NEW_CATEGORY . '</b>');

        $contents = array('form' => xtc_draw_form('newcategory', FILENAME_CATEGORIES, 'action=insert_category&cPath=' . $cPath, 'post', 'enctype="multipart/form-data"'));
        $contents[] = array('text' => TEXT_NEW_CATEGORY_INTRO);

        $category_inputs_string = '';
        $languages = xtc_get_languages();
        for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
          $category_inputs_string .= '<br>' . xtc_image(xarTplGetImage(DIR_WS_LANGUAGES . $languages[$i]['directory'] .'/'. $languages[$i]['image']), $languages[$i]['name']) . '&#160;' . xtc_draw_input_field('categories_name[' . $languages[$i]['id'] . ']');
        }

        $contents[] = array('text' => '<br>' . TEXT_CATEGORIES_NAME . $category_inputs_string);
        $contents[] = array('text' => '<br>' . TEXT_CATEGORIES_IMAGE . '<br>' . xtc_draw_file_field('categories_image'));
        $contents[] = array('text' => '<br>' . TEXT_SORT_ORDER . '<br>' . xtc_draw_input_field('sort_order', '', 'size="2"'));
        $contents[] = array('align' => 'center', 'text' => '<br>' . <input type="image" src="#xarTplGetImage('buttons/' . xarSession::getVar('language') . '/'.'button_save.gif')#" border="0" alt=IMAGE_SAVE> . ' <a href="' . xarModURL('commerce','admin',(FILENAME_CATEGORIES, 'cPath=' . $cPath) . '">' . xarModAPIFunc('commerce','user','image',array('src' => xarTplGetImage('buttons/' . xarSession::getVar('language') . '/'.'button_cancel.gif'),'alt' => IMAGE_CANCEL); . '</a>');
        break;

      case 'edit_category':
        $heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_EDIT_CATEGORY . '</b>');

        $contents = array('form' => xtc_draw_form('categories', FILENAME_CATEGORIES, 'action=update_category&cPath=' . $cPath, 'post', 'enctype="multipart/form-data"') .
  <input type="hidden" name="categories_id" value="#$cInfo->categories_id#">
        $contents[] = array('text' => TEXT_EDIT_INTRO);

        $category_inputs_string = '';
        $languages = xtc_get_languages();
        for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
          $category_inputs_string .= '<br>' . xtc_image(xarTplGetImage(DIR_WS_LANGUAGES . $languages[$i]['directory'] .'/'. $languages[$i]['image']), $languages[$i]['name']) . '&#160;' . xtc_draw_input_field('categories_name[' . $languages[$i]['id'] . ']', xtc_get_categories_name($cInfo->categories_id, $languages[$i]['id']));
        }

        $contents[] = array('text' => '<br>' . TEXT_EDIT_CATEGORIES_NAME . $category_inputs_string);
        $contents[] = array('text' => '<br>' . xtc_image(xarTplGetImage(DIR_WS_CATALOG_IMAGES . $cInfo->categories_image), $cInfo->categories_name) . '<br>' . DIR_WS_CATALOG_IMAGES . '<br><b>' . $cInfo->categories_image . '</b>');
        $contents[] = array('text' => '<br>' . TEXT_EDIT_CATEGORIES_IMAGE . '<br>' . xtc_draw_file_field('categories_image'));
        $contents[] = array('text' => '<br>' . TEXT_EDIT_SORT_ORDER . '<br>' . xtc_draw_input_field('sort_order', $cInfo->sort_order, 'size="2"'));
        $contents[] = array('text' => '<br>' . TEXT_EDIT_STATUS . '<br>' . xtc_draw_input_field('categories_status', $cInfo->categories_status, 'size="2"') . '1=Enabled 0=Disabled');
        $contents[] = array('align' => 'center', 'text' => '<br>' . <input type="image" src="#xarTplGetImage('buttons/' . xarSession::getVar('language') . '/'.'button_save.gif')#" border="0" alt=IMAGE_SAVE> . ' <a href="' . xarModURL('commerce','admin',(FILENAME_CATEGORIES, 'cPath=' . $cPath . '&cID=' . $cInfo->categories_id) . '">' . xarModAPIFunc('commerce','user','image',array('src' => xarTplGetImage('buttons/' . xarSession::getVar('language') . '/'.'button_cancel.gif'),'alt' => IMAGE_CANCEL); . '</a>');
        break;

      case 'delete_category':
        $heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_DELETE_CATEGORY . '</b>');

        $contents = array('form' => xtc_draw_form('categories', FILENAME_CATEGORIES, 'action=delete_category_confirm&cPath=' . $cPath) .
  <input type="hidden" name="categories_id" value="#$cInfo->categories_id#">
        $contents[] = array('text' => TEXT_DELETE_CATEGORY_INTRO);
        $contents[] = array('text' => '<br><b>' . $cInfo->categories_name . '</b>');
        if ($cInfo->childs_count > 0) $contents[] = array('text' => '<br>' . sprintf(TEXT_DELETE_WARNING_CHILDS, $cInfo->childs_count));
        if ($cInfo->products_count > 0) $contents[] = array('text' => '<br>' . sprintf(TEXT_DELETE_WARNING_PRODUCTS, $cInfo->products_count));
        $contents[] = array('align' => 'center', 'text' => '<br>' . <input type="image" src="#xarTplGetImage('buttons/' . xarSession::getVar('language') . '/'.'button_delete.gif')#" border="0" alt=IMAGE_DELETE> . ' <a href="' . xarModURL('commerce','admin',(FILENAME_CATEGORIES, 'cPath=' . $cPath . '&cID=' . $cInfo->categories_id) . '">' . xarModAPIFunc('commerce','user','image',array('src' => xarTplGetImage('buttons/' . xarSession::getVar('language') . '/'.'button_cancel.gif'),'alt' => IMAGE_CANCEL); . '</a>');
        break;

      case 'move_category':
        $heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_MOVE_CATEGORY . '</b>');

        $contents = array('form' => xtc_draw_form('categories', FILENAME_CATEGORIES, 'action=move_category_confirm') .
  <input type="hidden" name="categories_id" value="#$cInfo->categories_id#">
        $contents[] = array('text' => sprintf(TEXT_MOVE_CATEGORIES_INTRO, $cInfo->categories_name));
        $contents[] = array('text' => '<br>' . sprintf(TEXT_MOVE, $cInfo->categories_name) . '<br>' . commerce_userapi_draw_pull_down_menu('move_to_category_id', xtc_get_category_tree('0', '', $cInfo->categories_id), $current_category_id));
        $contents[] = array('align' => 'center', 'text' => '<br>' .
    <input type="image" src="#xarTplGetImage('buttons/' . xarSession::getVar('language') . '/'.'button_move.gif')#" border="0" alt=IMAGE_MOVE>
        <a href="' . xarModURL('commerce','admin',(FILENAME_CATEGORIES, 'cPath=' . $cPath . '&cID=' . $cInfo->categories_id) . '">' . xarModAPIFunc('commerce','user','image',array('src' => xarTplGetImage('buttons/' . xarSession::getVar('language') . '/'.'button_cancel.gif'),'alt' => IMAGE_CANCEL); . '</a>');
        break;

      case 'delete_product':
        $heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_DELETE_PRODUCT . '</b>');

        $contents = array('form' => xtc_draw_form('products', FILENAME_CATEGORIES, 'action=delete_product_confirm&cPath=' . $cPath) .
  <input type="hidden" name="products_id" value="#$pInfo->products_id#">
        $contents[] = array('text' => TEXT_DELETE_PRODUCT_INTRO);
        $contents[] = array('text' => '<br><b>' . $pInfo->products_name . '</b>');

        $product_categories_string = '';
        $product_categories = xtc_generate_category_path($pInfo->products_id, 'product');
        for ($i = 0, $n = sizeof($product_categories); $i < $n; $i++) {
          $category_path = '';
          for ($j = 0, $k = sizeof($product_categories[$i]); $j < $k; $j++) {
            $category_path .= $product_categories[$i][$j]['text'] . '&#160;&gt;&#160;';
          }
          $category_path = substr($category_path, 0, -16);
          $product_categories_string .= xtc_draw_checkbox_field('product_categories[]', $product_categories[$i][sizeof($product_categories[$i])-1]['id'], true) . '&#160;' . $category_path . '<br>';
        }
        $product_categories_string = substr($product_categories_string, 0, -4);

        $contents[] = array('text' => '<br>' . $product_categories_string);
        $contents[] = array('align' => 'center', 'text' => '<br>' . <input type="image" src="#xarTplGetImage('buttons/' . xarSession::getVar('language') . '/'.'button_delete.gif')#" border="0" alt=IMAGE_DELETE> . ' <a href="' . xarModURL('commerce','admin',(FILENAME_CATEGORIES, 'cPath=' . $cPath . '&pID=' . $pInfo->products_id) . '">' . xarModAPIFunc('commerce','user','image',array('src' => xarTplGetImage('buttons/' . xarSession::getVar('language') . '/'.'button_cancel.gif'),'alt' => IMAGE_CANCEL); . '</a>');
        break;

      case 'move_product':
        $heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_MOVE_PRODUCT . '</b>');

        $contents = array('form' => xtc_draw_form('products', FILENAME_CATEGORIES, 'action=move_product_confirm&cPath=' . $cPath) .
  <input type="hidden" name="products_id" value="#$pInfo->products_id#">
        $contents[] = array('text' => sprintf(TEXT_MOVE_PRODUCTS_INTRO, $pInfo->products_name));
        $contents[] = array('text' => '<br>' . TEXT_INFO_CURRENT_CATEGORIES . '<br><b>' . xtc_output_generated_category_path($pInfo->products_id, 'product') . '</b>');
        $contents[] = array('text' => '<br>' . sprintf(TEXT_MOVE, $pInfo->products_name) . '<br>' . commerce_userapi_draw_pull_down_menu('move_to_category_id', xtc_get_category_tree(), $current_category_id));
        $contents[] = array('align' => 'center', 'text' => '<br>' .
    <input type="image" src="#xarTplGetImage('buttons/' . xarSession::getVar('language') . '/'.'button_move.gif')#" border="0" alt=IMAGE_MOVE>.
        <a href="' . xarModURL('commerce','admin',(FILENAME_CATEGORIES, 'cPath=' . $cPath . '&pID=' . $pInfo->products_id) . '">' . xarModAPIFunc('commerce','user','image',array('src' => xarTplGetImage('buttons/' . xarSession::getVar('language') . '/'.'button_cancel.gif'),'alt' => IMAGE_CANCEL); . '</a>');
        break;

      case 'copy_to':
        $heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_COPY_TO . '</b>');

        $contents = array('form' => xtc_draw_form('copy_to', FILENAME_CATEGORIES, 'action=copy_to_confirm&cPath=' . $cPath) .
  <input type="hidden" name="products_id" value="#$pInfo->products_id#">
        $contents[] = array('text' => TEXT_INFO_COPY_TO_INTRO);
        $contents[] = array('text' => '<br>' . TEXT_INFO_CURRENT_CATEGORIES . '<br><b>' . xtc_output_generated_category_path($pInfo->products_id, 'product') . '</b>');
        $contents[] = array('text' => '<br>' . TEXT_CATEGORIES . '<br>' . commerce_userapi_draw_pull_down_menu('categories_id', xtc_get_category_tree(), $current_category_id));
        $contents[] = array('text' => '<br>' . TEXT_HOW_TO_COPY . '<br>' . xtc_draw_radio_field('copy_as', 'link', true) . ' ' . TEXT_COPY_AS_LINK . '<br>' . xtc_draw_radio_field('copy_as', 'duplicate') . ' ' . TEXT_COPY_AS_DUPLICATE);
        $contents[] = array('align' => 'center', 'text' => '<br>' .
    <input type="image" src="#xarTplGetImage('buttons/' . xarSession::getVar('language') . '/'.'button_copy.gif')#" border="0" alt=IMAGE_COPY>
        <a href="' . xarModURL('commerce','admin',(FILENAME_CATEGORIES, 'cPath=' . $cPath . '&pID=' . $pInfo->products_id) . '">' . xarModAPIFunc('commerce','user','image',array('src' => xarTplGetImage('buttons/' . xarSession::getVar('language') . '/'.'button_cancel.gif'),'alt' => IMAGE_CANCEL); . '</a>');
        break;

      default:
        if ($rows > 0) {
          if (is_object($cInfo)) { // category info box contents
            $heading[] = array('text' => '<b>' . $cInfo->categories_name . '</b>');

            $contents[] = array('align' => 'center', 'text' => '<a href="' . xarModURL('commerce','admin',(FILENAME_CATEGORIES, 'cPath=' . $cPath . '&cID=' . $cInfo->categories_id . '&action=edit_category') . '">' . xarModAPIFunc('commerce','user','image',array('src' => xarTplGetImage('buttons/' . xarSession::getVar('language') . '/'.'button_edit.gif'),'alt' => IMAGE_EDIT); . '</a> <a href="' . xarModURL('commerce','admin',(FILENAME_CATEGORIES, 'cPath=' . $cPath . '&cID=' . $cInfo->categories_id . '&action=delete_category') . '">' . xarModAPIFunc('commerce','user','image',array('src' => xarTplGetImage('buttons/' . xarSession::getVar('language') . '/'.'button_delete.gif'),'alt' => IMAGE_DELETE); . '</a> <a href="' . xarModURL('commerce','admin',(FILENAME_CATEGORIES, 'cPath=' . $cPath . '&cID=' . $cInfo->categories_id . '&action=move_category') . '">' .
         xarModAPIFunc('commerce','user','image',array('src' => xarTplGetImage('buttons/' . xarSession::getVar('language') . '/'.'button_move.gif'),'alt' => IMAGE_MOVE);
            </a>');
            $contents[] = array('text' => '<br>' . TEXT_DATE_ADDED . ' ' . xarModAPIFunc('commerce','user','date_short',array('raw_date' =>$cInfo->date_added)));
            if (xarModAPIFunc('commerce','user','not_null',array('arg' => $cInfo->last_modified))) $contents[] = array('text' => TEXT_LAST_MODIFIED . ' ' . xarModAPIFunc('commerce','user','date_short',array('raw_date' =>$cInfo->last_modified)));
            $contents[] = array('text' => '<br>' . xtc_info_image($cInfo->categories_image, $cInfo->categories_name, HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT) . '<br>' . $cInfo->categories_image);
            $contents[] = array('text' => '<br>' . TEXT_SUBCATEGORIES . ' ' . $cInfo->childs_count . '<br>' . TEXT_PRODUCTS . ' ' . $cInfo->products_count);
          } elseif (is_object($pInfo)) { // product info box contents
            $heading[] = array('text' => '<b>' . xarModAPIFunc('commerce','user','get_products_name',array('id' =>$pInfo->products_id, $_SESSION['languages_id'])) . '</b>');

            $contents[] = array('align' => 'center', 'text' => '<a href="' . xarModURL('commerce','admin',(FILENAME_CATEGORIES, 'cPath=' . $cPath . '&pID=' . $pInfo->products_id . '&action=new_product') . '">' . xarModAPIFunc('commerce','user','image',array('src' => xarTplGetImage('buttons/' . xarSession::getVar('language') . '/'.'button_edit.gif'),'alt' => IMAGE_EDIT); . '</a> <a href="' . xarModURL('commerce','admin',(FILENAME_CATEGORIES, 'cPath=' . $cPath . '&pID=' . $pInfo->products_id . '&action=delete_product') . '">' . xarModAPIFunc('commerce','user','image',array('src' => xarTplGetImage('buttons/' . xarSession::getVar('language') . '/'.'button_delete.gif'),'alt' => IMAGE_DELETE); . '</a> <a href="' . xarModURL('commerce','admin',(FILENAME_CATEGORIES, 'cPath=' . $cPath . '&pID=' . $pInfo->products_id . '&action=move_product') . '">' .
         xarModAPIFunc('commerce','user','image',array('src' => xarTplGetImage('buttons/' . xarSession::getVar('language') . '/'.'button_move.gif'),'alt' => IMAGE_MOVE);
            </a> <a href="' . xarModURL('commerce','admin',(FILENAME_CATEGORIES, 'cPath=' . $cPath . '&pID=' . $pInfo->products_id . '&action=copy_to') . '">' .
         xarModAPIFunc('commerce','user','image',array('src' => xarTplGetImage('buttons/' . xarSession::getVar('language') . '/'.'button_copy_to.gif'),'alt' => IMAGE_COPY_TO);
            </a><form action="' . FILENAME_NEW_ATTRIBUTES . '" name="edit_attributes" method="post">
            <input type="hidden" name="action" value="edit">
            <input type="hidden" name="current_product_id" value="#$pInfo->products_id#">
            <input type="hidden" name="cpath" value="#$cPath#">' .
    <input type="image" src="#xarTplGetImage('buttons/' . xarSession::getVar('language') . '/'.'button_edit_attributes.gif')#" border="0" alt='edit_attributes'>
            </form>');

            $contents[] = array('text' => '<br>' . TEXT_DATE_ADDED . ' ' . xarModAPIFunc('commerce','user','date_short',array('raw_date' =>$pInfo->products_date_added)));
            if (xarModAPIFunc('commerce','user','not_null',array('arg' => $pInfo->products_last_modified))) $contents[] = array('text' => TEXT_LAST_MODIFIED . ' ' . xarModAPIFunc('commerce','user','date_short',array('raw_date' =>$pInfo->products_last_modified)));
            if (date('Y-m-d') < $pInfo->products_date_available) $contents[] = array('text' => TEXT_DATE_AVAILABLE . ' ' . xarModAPIFunc('commerce','user','date_short',array('raw_date' =>$pInfo->products_date_available)));
            $contents[] = array('text' => '<br>' . xtc_product_info_image($pInfo->products_image, $pInfo->products_name, PRODUCT_IMAGE_INFO_WIDTH, PRODUCT_IMAGE_INFO_HEIGHT) . '<br>' . $pInfo->products_image);
                     // START IN-SOLUTION Berechung des Bruttopreises
            $price=$pInfo->products_price;
            $price=xtc_round($price,PRICE_PRECISION);
            $price_string=TEXT_PRODUCTS_PRICE_INFO . ' ' . $currencies->format($price);
            if (PRICE_IS_BRUTTO=='true' && ($_GET['read'] == 'only' || $_GET['action'] != 'new_product_preview') ){
                $price_netto=xtc_round($price,PRICE_PRECISION);
                $tax_query = new xenQuery("select tax_rate from " . TABLE_TAX_RATES . " where tax_class_id = '" . $pInfo->products_tax_class_id . "' ");
      $q = new xenQuery();
      if(!$q->run()) return;
                $tax = $q->output();
                $price= ($price*($tax[tax_rate]+100)/100);

                $price_string=TEXT_PRODUCTS_PRICE_INFO . ' ' . $currencies->format($price) . ' - ' . TXT_NETTO . $currencies->format($price_netto);

          }


            $contents[] = array('text' => '<br>' . $price_string. '<br>' .  TEXT_PRODUCTS_DISCOUNT_ALLOWED_INFO .  ' ' . $pInfo->products_discount_allowed . '<br>' . TEXT_PRODUCTS_QUANTITY_INFO . ' ' . $pInfo->products_quantity);
          // END IN-SOLUTION

//            $contents[] = array('text' => '<br>' . TEXT_PRODUCTS_PRICE_INFO . ' ' . $currencies->format($pInfo->products_price) . '<br>' . TEXT_PRODUCTS_QUANTITY_INFO . ' ' . $pInfo->products_quantity);
            $contents[] = array('text' => '<br>' . TEXT_PRODUCTS_AVERAGE_RATING . ' ' . number_format($pInfo->average_rating, 2) . '%');
          }
        } else { // create category/product info
          $heading[] = array('text' => '<b>' . EMPTY_CATEGORY . '</b>');

          $contents[] = array('text' => sprintf(TEXT_NO_CHILD_CATEGORIES_OR_PRODUCTS, $parent_categories_name));
        }
        break;
    }

    if ((xarModAPIFunc('commerce','user','not_null',array('arg' => $heading))) && (xarModAPIFunc('commerce','user','not_null',array('arg' => $contents)))) {
      echo '            <td width="25%" valign="top">' . "\n";

      $box = new box;
      echo $box->infoBox($heading, $contents);

      echo '            </td>' . "\n";
    }
?>
          </tr>
        </table></td>
      </tr>
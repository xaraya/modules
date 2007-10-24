<?php
/* --------------------------------------------------------------
   $Id: new_categorie.php,v 1.2 2003/12/22 20:30:36 fanta2k Exp $

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
    if ( ($_GET['cID']) && (!$_POST) ) {
      $category_query = new xenQuery("select c.categories_id, cd.language_id, cd.categories_name, cd.categories_heading_title, cd.categories_description, cd.categories_meta_title, cd.categories_meta_description, cd.categories_meta_keywords, c.categories_image, c.sort_order, c.date_added, c.last_modified from " . TABLE_CATEGORIES . " c, " . TABLE_CATEGORIES_DESCRIPTION . " cd where c.categories_id = cd.categories_id and c.categories_id = '" . $_GET['cID'] . "'");
      $q = new xenQuery();
      if(!$q->run()) return;
      $category = $q->output();

      $cInfo = new objectInfo($category);
    } elseif ($_POST) {
      $cInfo = new objectInfo($_POST);
      $categories_name = $_POST['categories_name'];
      $categories_heading_title = $_POST['categories_heading_title'];
      $categories_description = $_POST['categories_description'];
      $categories_meta_title = $_POST['categories_meta_title'];
      $categories_meta_description = $_POST['categories_meta_description'];
      $categories_meta_keywords = $_POST['categories_meta_keywords'];
      $categories_url = $_POST['categories_url'];
    } else {
      $cInfo = new objectInfo(array());
    }

    $languages = xtc_get_languages();

    $text_new_or_edit = ($_GET['action']=='new_category_ACD') ? TEXT_INFO_HEADING_NEW_CATEGORY : TEXT_INFO_HEADING_EDIT_CATEGORY;
?>
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td class="pageHeading"><?php echo sprintf($text_new_or_edit, xtc_output_generated_category_path($current_category_id)); ?></td>
            <td class="pageHeading" align="right"><?php echo xtc_draw_separator('pixel_trans.gif', HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT); ?></td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td><?php echo xtc_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
      </tr>
      <?php
          $form_action = ($_GET['cID']) ? 'update_category' : 'insert_category';
    echo xtc_draw_form('new_category', FILENAME_CATEGORIES, 'cPath=' . $cPath . '&cID=' . $_GET['cID'] . '&action='.$form_action, 'post', 'enctype="multipart/form-data"'); ?>
        <td><table border="0" cellspacing="0" cellpadding="2">

<?php    for ($i=0; $i<sizeof($languages); $i++) { ?>
          <tr>
            <td class="main"><?php if ($i == 0) echo TEXT_EDIT_CATEGORIES_NAME; ?></td>
            <td class="main"><?php echo xtc_image(xarTplGetImage(DIR_WS_LANGUAGES.$languages[$i]['directory'].'/admin/images/'.$languages[$i]['image']) . '&#160;' . xtc_draw_input_field('categories_name[' . $languages[$i]['id'] . ']', (($categories_name[$languages[$i]['id']]) ? stripslashes($categories_name[$languages[$i]['id']]) : xtc_get_categories_name($cInfo->categories_id, $languages[$i]['id']))); ?></td>
          </tr>
<?php } ?>

          <tr><td colspan="2"><?php echo xtc_draw_separator('pixel_trans.gif', '1', '10'); ?></td></tr>

<?php    for ($i=0; $i<sizeof($languages); $i++) { ?>
          <tr>
            <td class="main"><?php if ($i == 0) echo TEXT_EDIT_CATEGORIES_HEADING_TITLE; ?></td>
            <td class="main"><?php echo xtc_image(xarTplGetImage(DIR_WS_LANGUAGES.$languages[$i]['directory'].'/admin/images/'.$languages[$i]['image']) . '&#160;' . xtc_draw_input_field('categories_heading_title[' . $languages[$i]['id'] . ']', (($categories_name[$languages[$i]['id']]) ? stripslashes($categories_name[$languages[$i]['id']]) : xtc_get_categories_heading_title($cInfo->categories_id, $languages[$i]['id']))); ?></td>
          </tr>
<?php } ?>

        <tr><td colspan="2"><?php echo xtc_draw_separator('pixel_trans.gif', '1', '10'); ?></td></tr>

<?php    for ($i=0; $i<sizeof($languages); $i++) { ?>
          <tr>
            <td class="main" valign="top"><?php  echo TEXT_EDIT_CATEGORIES_DESCRIPTION; ?></td>
            <td><table border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td class="main" valign="top"><?php echo xtc_image(xarTplGetImage(DIR_WS_LANGUAGES.$languages[$i]['directory'].'/admin/images/'.$languages[$i]['image']); ?>&#160;</td>
                <td class="main"><?php echo xtc_draw_textarea_field('categories_description[' . $languages[$i]['id'] . ']', 'soft', '70', '15', (($categories_description[$languages[$i]['id']]) ? stripslashes($categories_description[$languages[$i]['id']]) : xtc_get_categories_description($cInfo->categories_id, $languages[$i]['id']))); ?></td>
              </tr>
            </table></td>
          </tr>
          <tr>
            <td class="main" valign="top"><?php  echo TEXT_META_TITLE; ?></td>
            <td><table border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td class="main" valign="top"><?php echo xtc_image(xarTplGetImage(DIR_WS_LANGUAGES.$languages[$i]['directory'].'/admin/images/'.$languages[$i]['image']); ?>&#160;</td>
                <td class="main"><?php echo xtc_draw_textarea_field('categories_meta_title[' . $languages[$i]['id'] . ']', 'soft', '70', '2', (($categories_meta_title[$languages[$i]['id']]) ? stripslashes($categories_meta_title[$languages[$i]['id']]) : xtc_get_categories_meta_title($cInfo->categories_id, $languages[$i]['id']))); ?></td>
              </tr>
            </table></td>
          </tr>
          <tr>
            <td colspan="2"><?php echo xtc_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
          </tr>
           <tr>
            <td class="main" valign="top"><?php  echo TEXT_META_DESCRIPTION; ?></td>
            <td><table border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td class="main" valign="top"><?php echo xtc_image(xarTplGetImage(DIR_WS_LANGUAGES.$languages[$i]['directory'].'/admin/images/'.$languages[$i]['image']); ?>&#160;</td>
                <td class="main"><?php echo xtc_draw_textarea_field('categories_meta_description[' . $languages[$i]['id'] . ']', 'soft', '70', '3', (($categories_meta_description[$languages[$i]['id']]) ? stripslashes($categories_meta_description[$languages[$i]['id']]) : xtc_get_categories_meta_description($cInfo->categories_id, $languages[$i]['id']))); ?></td>
              </tr>
            </table></td>
          </tr>
          <tr>
            <td colspan="2"><?php echo xtc_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
          </tr>
           <tr>
            <td class="main" valign="top"><?php  echo TEXT_META_KEYWORDS; ?></td>
            <td><table border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td class="main" valign="top"><?php echo xtc_image(xarTplGetImage(DIR_WS_LANGUAGES.$languages[$i]['directory'].'/admin/images/'.$languages[$i]['image']); ?>&#160;</td>
                <td class="main"><?php echo xtc_draw_textarea_field('categories_meta_keywords[' . $languages[$i]['id'] . ']', 'soft', '70', '3', (($categories_meta_keywords[$languages[$i]['id']]) ? stripslashes($categories_meta_keywords[$languages[$i]['id']]) : xtc_get_categories_meta_keywords($cInfo->categories_id, $languages[$i]['id']))); ?></td>
              </tr>
            </table></td>
          </tr>
<?php } ?>
        <tr><td colspan="2"><?php echo xtc_draw_separator('pixel_trans.gif', '1', '10'); ?></td></tr>
          <tr>
            <td class="main"><?php echo TEXT_EDIT_CATEGORIES_IMAGE; ?></td>
            <td class="main"><?php echo xtc_draw_separator('pixel_trans.gif', '24', '15') . '&#160;' . xtc_draw_file_field('categories_image') . '<br>' . xtc_draw_separator('pixel_trans.gif', '24', '15') . '&#160;' . $cInfo->categories_image .
<input type="hidden" name="categories_previous_image" value="#$cInfo->categories_image#">
            </td>
          </tr>
          <tr><td colspan="2"><?php echo xtc_draw_separator('pixel_trans.gif', '1', '10'); ?></td></tr>
          <tr>
            <td class="main"><?php echo TEXT_EDIT_SORT_ORDER; ?></td>
            <td class="main"><?php echo xtc_draw_separator('pixel_trans.gif', '24', '15') . '&#160;' . xtc_draw_input_field('sort_order', $cInfo->sort_order, 'size="2"'); ?></td>
          </tr>
           <tr><td colspan="2"><?php echo xtc_draw_separator('pixel_trans.gif', '1', '10'); ?></td></tr>
        </table></td>
      </tr>
      <tr>
        <td class="main" align="right">
<input type="hidden" name="categories_date_added" value="#(($cInfo->date_added) ? $cInfo->date_added : date('Y-m-d'))#">
<input type="hidden" name="parent_id" value="#$cInfo->parent_id#">
    <input type="image" src="#xarTplGetImage('buttons/' . xarSession::getVar('language') . '/'.'button_save.gif')#" border="0" alt=IMAGE_SAVE style="cursor:hand" onclick="return confirm(\''.SAVE_ENTRY.'\')">.
        . '&#160;&#160;<a href="' . xarModURL('commerce','admin',(FILENAME_CATEGORIES, 'cPath=' . $cPath . '&cID=' . $_GET['cID']) . '">' . xarModAPIFunc('commerce','user','image',array('src' => xarTplGetImage('buttons/' . xarSession::getVar('language') . '/'.'button_cancel.gif'),'alt' => IMAGE_CANCEL); . '</a>'; ?></td>
      </form></tr>
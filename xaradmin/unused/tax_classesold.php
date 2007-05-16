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

function commerce_admin_tax_classes()
{


  if ($_GET['action']) {
    switch ($_GET['action']) {
      case 'insert':
        $tax_class_title = xtc_db_prepare_input($_POST['tax_class_title']);
        $tax_class_description = xtc_db_prepare_input($_POST['tax_class_description']);
        $date_added = xtc_db_prepare_input($_POST['date_added']);

        new xenQuery("insert into " . TABLE_TAX_CLASS . " (tax_class_title, tax_class_description, date_added) values ('" . xtc_db_input($tax_class_title) . "', '" . xtc_db_input($tax_class_description) . "', now())");
        xarRedirectResponse(xarModURL('commerce','admin',(FILENAME_TAX_CLASSES));
        break;

      case 'save':
        $tax_class_id = xtc_db_prepare_input($_GET['tID']);
        $tax_class_title = xtc_db_prepare_input($_POST['tax_class_title']);
        $tax_class_description = xtc_db_prepare_input($_POST['tax_class_description']);
        $last_modified = xtc_db_prepare_input($_POST['last_modified']);

        new xenQuery("update " . TABLE_TAX_CLASS . " set tax_class_id = '" . xtc_db_input($tax_class_id) . "', tax_class_title = '" . xtc_db_input($tax_class_title) . "', tax_class_description = '" . xtc_db_input($tax_class_description) . "', last_modified = now() where tax_class_id = '" . xtc_db_input($tax_class_id) . "'");
        xarRedirectResponse(xarModURL('commerce','admin',(FILENAME_TAX_CLASSES, 'page=' . $_GET['page'] . '&tID=' . $tax_class_id));
        break;

      case 'deleteconfirm':
        $tax_class_id = xtc_db_prepare_input($_GET['tID']);

        new xenQuery("delete from " . TABLE_TAX_CLASS . " where tax_class_id = '" . xtc_db_input($tax_class_id) . "'");
        xarRedirectResponse(xarModURL('commerce','admin',(FILENAME_TAX_CLASSES, 'page=' . $_GET['page']));
        break;
    }
  }

  $classes_query_raw = "select tax_class_id, tax_class_title, tax_class_description, last_modified, date_added from " . TABLE_TAX_CLASS . " order by tax_class_title";
  $classes_split = new splitPageResults($_GET['page'], MAX_DISPLAY_SEARCH_RESULTS, $classes_query_raw, $classes_query_numrows);
  $classes_query = new xenQuery($classes_query_raw);
      $q = new xenQuery();
      if(!$q->run()) return;
  while ($classes = $q->output()) {
    if (((!$_GET['tID']) || (@$_GET['tID'] == $classes['tax_class_id'])) && (!$tcInfo) && (substr($_GET['action'], 0, 3) != 'new')) {
      $tcInfo = new objectInfo($classes);
    }

    if ( (is_object($tcInfo)) && ($classes['tax_class_id'] == $tcInfo->tax_class_id) ) {
      echo '              <tr class="dataTableRowSelected" onmouseover="this.style.cursor=\'hand\'" onclick="document.location.href=\'' . xarModURL('commerce','admin',(FILENAME_TAX_CLASSES, 'page=' . $_GET['page'] . '&tID=' . $tcInfo->tax_class_id . '&action=edit') . '\'">' . "\n";
    } else {
      echo'              <tr class="dataTableRow" onmouseover="this.className=\'dataTableRowOver\';this.style.cursor=\'hand\'" onmouseout="this.className=\'dataTableRow\'" onclick="document.location.href=\'' . xarModURL('commerce','admin',(FILENAME_TAX_CLASSES, 'page=' . $_GET['page'] . '&tID=' . $classes['tax_class_id']) . '\'">' . "\n";
    }
?>
                <td class="dataTableContent"><?php echo $classes['tax_class_title']; ?></td>
                <td class="dataTableContent" align="right"><?php if ( (is_object($tcInfo)) && ($classes['tax_class_id'] == $tcInfo->tax_class_id) ) { echo xtc_image(xarTplGetImage(DIR_WS_IMAGES . 'icon_arrow_right.gif'), ''); } else { echo '<a href="' . xarModURL('commerce','admin',(FILENAME_TAX_CLASSES, 'page=' . $_GET['page'] . '&tID=' . $classes['tax_class_id']) . '">' . xtc_image(xarTplGetImage(DIR_WS_IMAGES . 'icon_info.gif'), IMAGE_ICON_INFO) . '</a>'; } ?>&#160;</td>
              </tr>
<?php
  }
?>
              <tr>
                <td colspan="2"><table border="0" width="100%" cellspacing="0" cellpadding="2">
                  <tr>
                    <td class="smallText" valign="top"><?php echo $classes_split->display_count($classes_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_TAX_CLASSES); ?></td>
                    <td class="smallText" align="right"><?php echo $classes_split->display_links($classes_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, MAX_DISPLAY_PAGE_LINKS, $_GET['page']); ?></td>
                  </tr>
<?php
  if (!$_GET['action']) {
?>
                  <tr>
                    <td colspan="2" align="right"><?php echo '<a href="' . xarModURL('commerce','admin',(FILENAME_TAX_CLASSES, 'page=' . $_GET['page'] . '&action=new') . '">' .
xarModAPIFunc('commerce','user','image',array('src' => xarTplGetImage('buttons/' . xarSession::getVar('language') . '/'.'button_new_tax_class.gif'),'alt' => IMAGE_NEW_TAX_CLASS)                                                                    </a>'; ?></td>
                  </tr>
<?php
  }
?>
                </table></td>
              </tr>
            </table></td>
<?php
  $heading = array();
  $contents = array();
  switch ($_GET['action']) {
    case 'new':
      $heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_NEW_TAX_CLASS . '</b>');

      $contents = array('form' => xtc_draw_form('classes', FILENAME_TAX_CLASSES, 'page=' . $_GET['page'] . '&action=insert'));
      $contents[] = array('text' => TEXT_INFO_INSERT_INTRO);
      $contents[] = array('text' => '<br>' . TEXT_INFO_CLASS_TITLE . '<br>' . xtc_draw_input_field('tax_class_title'));
      $contents[] = array('text' => '<br>' . TEXT_INFO_CLASS_DESCRIPTION . '<br>' . xtc_draw_input_field('tax_class_description'));
      $contents[] = array('align' => 'center', 'text' => '<br>' . <input type="image" src="#xarTplGetImage('buttons/' . xarSession::getVar('language') . '/'.'button_insert.gif')#" border="0" alt=IMAGE_INSERT> . '&#160;<a href="' . xarModURL('commerce','admin',(FILENAME_TAX_CLASSES, 'page=' . $_GET['page']) . '">' . xarModAPIFunc('commerce','user','image',array('src' => xarTplGetImage('buttons/' . xarSession::getVar('language') . '/'.'button_cancel.gif'),'alt' => IMAGE_CANCEL); . '</a>');
      break;

    case 'edit':
      $heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_EDIT_TAX_CLASS . '</b>');

      $contents = array('form' => xtc_draw_form('classes', FILENAME_TAX_CLASSES, 'page=' . $_GET['page'] . '&tID=' . $tcInfo->tax_class_id . '&action=save'));
      $contents[] = array('text' => TEXT_INFO_EDIT_INTRO);
      $contents[] = array('text' => '<br>' . TEXT_INFO_CLASS_TITLE . '<br>' . xtc_draw_input_field('tax_class_title', $tcInfo->tax_class_title));
      $contents[] = array('text' => '<br>' . TEXT_INFO_CLASS_DESCRIPTION . '<br>' . xtc_draw_input_field('tax_class_description', $tcInfo->tax_class_description));
      $contents[] = array('align' => 'center', 'text' => '<br>' . <input type="image" src="#xarTplGetImage('buttons/' . xarSession::getVar('language') . '/'.'button_update.gif')#" border="0" alt=IMAGE_UPDATE> . '&#160;<a href="' . xarModURL('commerce','admin',(FILENAME_TAX_CLASSES, 'page=' . $_GET['page'] . '&tID=' . $tcInfo->tax_class_id) . '">' . xarModAPIFunc('commerce','user','image',array('src' => xarTplGetImage('buttons/' . xarSession::getVar('language') . '/'.'button_cancel.gif'),'alt' => IMAGE_CANCEL); . '</a>');
      break;

    case 'delete':
      $heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_DELETE_TAX_CLASS . '</b>');

      $contents = array('form' => xtc_draw_form('classes', FILENAME_TAX_CLASSES, 'page=' . $_GET['page'] . '&tID=' . $tcInfo->tax_class_id . '&action=deleteconfirm'));
      $contents[] = array('text' => TEXT_INFO_DELETE_INTRO);
      $contents[] = array('text' => '<br><b>' . $tcInfo->tax_class_title . '</b>');
      $contents[] = array('align' => 'center', 'text' => '<br>' . <input type="image" src="#xarTplGetImage('buttons/' . xarSession::getVar('language') . '/'.'button_delete.gif')#" border="0" alt=IMAGE_DELETE> . '&#160;<a href="' . xarModURL('commerce','admin',(FILENAME_TAX_CLASSES, 'page=' . $_GET['page'] . '&tID=' . $tcInfo->tax_class_id) . '">' . xarModAPIFunc('commerce','user','image',array('src' => xarTplGetImage('buttons/' . xarSession::getVar('language') . '/'.'button_cancel.gif'),'alt' => IMAGE_CANCEL); . '</a>');
      break;

    default:
      if (is_object($tcInfo)) {
        $heading[] = array('text' => '<b>' . $tcInfo->tax_class_title . '</b>');

        $contents[] = array('align' => 'center', 'text' => '<a href="' . xarModURL('commerce','admin',(FILENAME_TAX_CLASSES, 'page=' . $_GET['page'] . '&tID=' . $tcInfo->tax_class_id . '&action=edit') . '">' . xarModAPIFunc('commerce','user','image',array('src' => xarTplGetImage('buttons/' . xarSession::getVar('language') . '/'.'button_edit.gif'),'alt' => IMAGE_EDIT); . '</a> <a href="' . xarModURL('commerce','admin',(FILENAME_TAX_CLASSES, 'page=' . $_GET['page'] . '&tID=' . $tcInfo->tax_class_id . '&action=delete') . '">' . xarModAPIFunc('commerce','user','image',array('src' => xarTplGetImage('buttons/' . xarSession::getVar('language') . '/'.'button_delete.gif'),'alt' => IMAGE_DELETE); . '</a>');
        $contents[] = array('text' => '<br>' . TEXT_INFO_DATE_ADDED . ' ' . xarModAPIFunc('commerce','user','date_short',array('raw_date' =>$tcInfo->date_added)));
        $contents[] = array('text' => '' . TEXT_INFO_LAST_MODIFIED . ' ' . xarModAPIFunc('commerce','user','date_short',array('raw_date' =>$tcInfo->last_modified)));
        $contents[] = array('text' => '<br>' . TEXT_INFO_CLASS_DESCRIPTION . '<br>' . $tcInfo->tax_class_description);
      }
      break;
  }
  if ( (xarModAPIFunc('commerce','user','not_null',array('arg' => $heading))) && (xarModAPIFunc('commerce','user','not_null',array('arg' => $contents))) ) {
    echo '            <td width="25%" valign="top">' . "\n";

    $box = new box;
    echo $box->infoBox($heading, $contents);

    echo '            </td>' . "\n";
  }
}
?>
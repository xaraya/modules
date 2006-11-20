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

function commerce_admin_geo_zones()
{


  switch ($_GET['saction']) {
    case 'insert_sub':
      $zID = xtc_db_prepare_input($_GET['zID']);
      $zone_country_id = xtc_db_prepare_input($_POST['zone_country_id']);
      $zone_id = xtc_db_prepare_input($_POST['zone_id']);

      new xenQuery("insert into " . TABLE_ZONES_TO_GEO_ZONES . " (zone_country_id, zone_id, geo_zone_id, date_added) values ('" . xtc_db_input($zone_country_id) . "', '" . xtc_db_input($zone_id) . "', '" . xtc_db_input($zID) . "', now())");
      $new_subzone_id = xtc_db_insert_id();

      xarRedirectResponse(xarModURL('commerce','admin',(FILENAME_GEO_ZONES, 'zpage=' . $_GET['zpage'] . '&zID=' . $_GET['zID'] . '&action=list&spage=' . $_GET['spage'] . '&sID=' . $new_subzone_id));
      break;

    case 'save_sub':
      $sID = xtc_db_prepare_input($_GET['sID']);
      $zID = xtc_db_prepare_input($_GET['zID']);
      $zone_country_id = xtc_db_prepare_input($_POST['zone_country_id']);
      $zone_id = xtc_db_prepare_input($_POST['zone_id']);

      new xenQuery("update " . TABLE_ZONES_TO_GEO_ZONES . " set geo_zone_id = '" . xtc_db_input($zID) . "', zone_country_id = '" . xtc_db_input($zone_country_id) . "', zone_id = " . ((xtc_db_input($zone_id)) ? "'" . xtc_db_input($zone_id) . "'" : 'null') . ", last_modified = now() where association_id = '" . xtc_db_input($sID) . "'");

      xarRedirectResponse(xarModURL('commerce','admin',(FILENAME_GEO_ZONES, 'zpage=' . $_GET['zpage'] . '&zID=' . $_GET['zID'] . '&action=list&spage=' . $_GET['spage'] . '&sID=' . $_GET['sID']));
      break;

    case 'deleteconfirm_sub':
      $sID = xtc_db_prepare_input($_GET['sID']);

      new xenQuery("delete from " . TABLE_ZONES_TO_GEO_ZONES . " where association_id = '" . xtc_db_input($sID) . "'");

      xarRedirectResponse(xarModURL('commerce','admin',(FILENAME_GEO_ZONES, 'zpage=' . $_GET['zpage'] . '&zID=' . $_GET['zID'] . '&action=list&spage=' . $_GET['spage']));
      break;
  }

  switch ($_GET['action']) {
    case 'insert_zone':
      $geo_zone_name = xtc_db_prepare_input($_POST['geo_zone_name']);
      $geo_zone_description = xtc_db_prepare_input($_POST['geo_zone_description']);

      new xenQuery("insert into " . TABLE_GEO_ZONES . " (geo_zone_name, geo_zone_description, date_added) values ('" . xtc_db_input($geo_zone_name) . "', '" . xtc_db_input($geo_zone_description) . "', now())");
      $new_zone_id = xtc_db_insert_id();

      xarRedirectResponse(xarModURL('commerce','admin',(FILENAME_GEO_ZONES, 'zpage=' . $_GET['zpage'] . '&zID=' . $new_zone_id));
      break;

    case 'save_zone':
      $zID = xtc_db_prepare_input($_GET['zID']);
      $geo_zone_name = xtc_db_prepare_input($_POST['geo_zone_name']);
      $geo_zone_description = xtc_db_prepare_input($_POST['geo_zone_description']);

      new xenQuery("update " . TABLE_GEO_ZONES . " set geo_zone_name = '" . xtc_db_input($geo_zone_name) . "', geo_zone_description = '" . xtc_db_input($geo_zone_description) . "', last_modified = now() where geo_zone_id = '" . xtc_db_input($zID) . "'");

      xarRedirectResponse(xarModURL('commerce','admin',(FILENAME_GEO_ZONES, 'zpage=' . $_GET['zpage'] . '&zID=' . $_GET['zID']));
      break;

    case 'deleteconfirm_zone':
      $zID = xtc_db_prepare_input($_GET['zID']);

      new xenQuery("delete from " . TABLE_GEO_ZONES . " where geo_zone_id = '" . xtc_db_input($zID) . "'");
      new xenQuery("delete from " . TABLE_ZONES_TO_GEO_ZONES . " where geo_zone_id = '" . xtc_db_input($zID) . "'");

      xarRedirectResponse(xarModURL('commerce','admin',(FILENAME_GEO_ZONES, 'zpage=' . $_GET['zpage']));
      break;
  }

  if ($_GET['zID']  && (($_GET['saction'] == 'edit') || ($_GET['saction'] == 'new'))) {
?>
<script language="javascript"><!--
function resetZoneSelected(theForm) {
  if (theForm.state.value != '') {
    theForm.zone_id.selectedIndex = '0';
    if (theForm.zone_id.options.length > 0) {
      theForm.state.value = '<?php echo JS_STATE_SELECT; ?>';
    }
  }
}

function update_zone(theForm) {
  var NumState = theForm.zone_id.options.length;
  var SelectedCountry = "";

  while(NumState > 0) {
    NumState--;
    theForm.zone_id.options[NumState] = null;
  }

  SelectedCountry = theForm.zone_country_id.options[theForm.zone_country_id.selectedIndex].value;

<?php echo xtc_js_zone_list('SelectedCountry', 'theForm', 'zone_id'); ?>

}
//--></script>
<?php
  }
?>
</head>
<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF" onload="SetFocus();">
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
    <td width="100%" valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td width="100%"><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td class="pageHeading"><?php echo HEADING_TITLE; if ($_GET['zone']) echo '<br><span class="smallText">' . xtc_get_geo_zone_name($_GET['zone']) . '</span>'; ?></td>
            <td class="pageHeading" align="right"><?php echo xtc_draw_separator('pixel_trans.gif', HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT); ?></td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td valign="top">
<?php
  if ($_GET['action'] == 'list') {
?>
            <table border="0" width="100%" cellspacing="0" cellpadding="2">
              <tr class="dataTableHeadingRow">
                <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_COUNTRY; ?></td>
                <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_COUNTRY_ZONE; ?></td>
                <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_ACTION; ?>&#160;</td>
              </tr>
<?php
    $rows = 0;
    $zones_query_raw = "select a.association_id, a.zone_country_id, c.countries_name, a.zone_id, a.geo_zone_id, a.last_modified, a.date_added, z.zone_name from " . TABLE_ZONES_TO_GEO_ZONES . " a left join " . TABLE_COUNTRIES . " c on a.zone_country_id = c.countries_id left join " . TABLE_ZONES . " z on a.zone_id = z.zone_id where a.geo_zone_id = " . $_GET['zID'] . " order by association_id";
    $zones_split = new splitPageResults($_GET['spage'], MAX_DISPLAY_SEARCH_RESULTS, $zones_query_raw, $zones_query_numrows);
    $zones_query = new xenQuery($zones_query_raw);
      $q = new xenQuery();
      if(!$q->run()) return;
    while ($zones = $q->output()) {
      $rows++;
      if (((!$_GET['sID']) || (@$_GET['sID'] == $zones['association_id'])) && (!$sInfo) && (substr($_GET['saction'], 0, 3) != 'new')) {
        $sInfo = new objectInfo($zones);
      }
      if ( (is_object($sInfo)) && ($zones['association_id'] == $sInfo->association_id) ) {
        echo '                  <tr class="dataTableRowSelected" onmouseover="this.style.cursor=\'hand\'" onclick="document.location.href=\'' . xarModURL('commerce','admin',(FILENAME_GEO_ZONES, 'zpage=' . $_GET['zpage'] . '&zID=' . $_GET['zID'] . '&action=list&spage=' . $_GET['spage'] . '&sID=' . $sInfo->association_id . '&saction=edit') . '\'">' . "\n";
      } else {
        echo '                  <tr class="dataTableRow" onmouseover="this.className=\'dataTableRowOver\';this.style.cursor=\'hand\'" onmouseout="this.className=\'dataTableRow\'" onclick="document.location.href=\'' . xarModURL('commerce','admin',(FILENAME_GEO_ZONES, 'zpage=' . $_GET['zpage'] . '&zID=' . $_GET['zID'] . '&action=list&spage=' . $_GET['spage'] . '&sID=' . $zones['association_id']) . '\'">' . "\n";
      }
?>
                <td class="dataTableContent"><?php echo (($zones['countries_name']) ? $zones['countries_name'] : TEXT_ALL_COUNTRIES); ?></td>
                <td class="dataTableContent"><?php echo (($zones['zone_id']) ? $zones['zone_name'] : PLEASE_SELECT); ?></td>
                <td class="dataTableContent" align="right"><?php if ( (is_object($sInfo)) && ($zones['association_id'] == $sInfo->association_id) ) { echo xtc_image(xarTplGetImage(DIR_WS_IMAGES . 'icon_arrow_right.gif'), ''); } else { echo '<a href="' . xarModURL('commerce','admin',(FILENAME_GEO_ZONES, 'zpage=' . $_GET['zpage'] . '&zID=' . $_GET['zID'] . '&action=list&spage=' . $_GET['spage'] . '&sID=' . $zones['association_id']) . '">' . xtc_image(xarTplGetImage(DIR_WS_IMAGES . 'icon_info.gif', IMAGE_ICON_INFO) . '</a>'; } ?>&#160;</td>
              </tr>
<?php
    }
?>
              <tr>
                <td colspan="3"><table border="0" width="100%" cellspacing="0" cellpadding="2">
                  <tr>
                    <td class="smallText" valign="top"><?php echo $zones_split->display_count($zones_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, $_GET['spage'], TEXT_DISPLAY_NUMBER_OF_COUNTRIES); ?></td>
                    <td class="smallText" align="right"><?php echo $zones_split->display_links($zones_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, MAX_DISPLAY_PAGE_LINKS, $_GET['spage'], 'zpage=' . $_GET['zpage'] . '&zID=' . $_GET['zID'] . '&action=list', 'spage'); ?></td>
                  </tr>
                </table></td>
              </tr>
              <tr>
                <td align="right" colspan="3"><?php if (!$_GET['saction']) echo '<a href="' . xarModURL('commerce','admin',(FILENAME_GEO_ZONES, 'zpage=' . $_GET['zpage'] . '&zID=' . $_GET['zID']) . '">' . xarModAPIFunc('commerce','user','image',array('src' => xarTplGetImage('buttons/' . xarSessionGetVar('language') . '/'.'button_back.gif'),'alt' => IMAGE_NEW_BACK); . '</a> <a href="' . xarModURL('commerce','admin',(FILENAME_GEO_ZONES, 'zpage=' . $_GET['zpage'] . '&zID=' . $_GET['zID'] . '&action=list&spage=' . $_GET['spage'] . '&sID=' . $sInfo->association_id . '&saction=new') . '">' . xarModAPIFunc('commerce','user','image',array('src' => xarTplGetImage('buttons/' . xarSessionGetVar('language') . '/'.'button_insert.gif'),'alt' => IMAGE_INSERT); . '</a>'; ?></td>
              </tr>
            </table>
<?php
  } else {
?>
            <table border="0" width="100%" cellspacing="0" cellpadding="2">
              <tr class="dataTableHeadingRow">
                <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_TAX_ZONES; ?></td>
                <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_ACTION; ?>&#160;</td>
              </tr>
<?php
    $zones_query_raw = "select geo_zone_id, geo_zone_name, geo_zone_description, last_modified, date_added from " . TABLE_GEO_ZONES . " order by geo_zone_name";
    $zones_split = new splitPageResults($_GET['zpage'], MAX_DISPLAY_SEARCH_RESULTS, $zones_query_raw, $zones_query_numrows);
    $zones_query = new xenQuery($zones_query_raw);
      $q = new xenQuery();
      if(!$q->run()) return;
    while ($zones = $q->output()) {
      if (((!$_GET['zID']) || (@$_GET['zID'] == $zones['geo_zone_id'])) && (!$zInfo) && (substr($_GET['action'], 0, 3) != 'new')) {
        $num_zones_query = new xenQuery("select count(*) as num_zones from " . TABLE_ZONES_TO_GEO_ZONES . " where geo_zone_id = '" . $zones['geo_zone_id'] . "' group by geo_zone_id");
        if ($num_zones_query->getrows() > 0) {
      $q = new xenQuery();
      if(!$q->run()) return;
          $num_zones = $q->output();
          $zones['num_zones'] = $num_zones['num_zones'];
        } else {
          $zones['num_zones'] = 0;
        }
        $zInfo = new objectInfo($zones);
      }
      if ( (is_object($zInfo)) && ($zones['geo_zone_id'] == $zInfo->geo_zone_id) ) {
        echo '                  <tr class="dataTableRowSelected" onmouseover="this.style.cursor=\'hand\'" onclick="document.location.href=\'' . xarModURL('commerce','admin',(FILENAME_GEO_ZONES, 'zpage=' . $_GET['zpage'] . '&zID=' . $zInfo->geo_zone_id . '&action=list') . '\'">' . "\n";
      } else {
        echo '                  <tr class="dataTableRow" onmouseover="this.className=\'dataTableRowOver\';this.style.cursor=\'hand\'" onmouseout="this.className=\'dataTableRow\'" onclick="document.location.href=\'' . xarModURL('commerce','admin',(FILENAME_GEO_ZONES, 'zpage=' . $_GET['zpage'] . '&zID=' . $zones['geo_zone_id']) . '\'">' . "\n";
      }
?>
                <td class="dataTableContent"><?php echo '<a href="' . xarModURL('commerce','admin',(FILENAME_GEO_ZONES, 'zpage=' . $_GET['zpage'] . '&zID=' . $zones['geo_zone_id'] . '&action=list') . '">' . xtc_image(xarTplGetImage('icons/folder.gif'), ICON_FOLDER) . '</a>&#160;' . $zones['geo_zone_name']; ?></td>
                <td class="dataTableContent" align="right"><?php if ( (is_object($zInfo)) && ($zones['geo_zone_id'] == $zInfo->geo_zone_id) ) { echo xtc_image(xarTplGetImage(DIR_WS_IMAGES . 'icon_arrow_right.gif'); } else { echo '<a href="' . xarModURL('commerce','admin',(FILENAME_GEO_ZONES, 'zpage=' . $_GET['zpage'] . '&zID=' . $zones['geo_zone_id']) . '">' . xtc_image(xarTplGetImage(DIR_WS_IMAGES . 'icon_info.gif', IMAGE_ICON_INFO) . '</a>'; } ?>&#160;</td>
              </tr>
<?php
    }
?>
              <tr>
                <td colspan="2"><table border="0" width="100%" cellspacing="0" cellpadding="2">
                  <tr>
                    <td class="smallText"><?php echo $zones_split->display_count($zones_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, $_GET['zpage'], TEXT_DISPLAY_NUMBER_OF_TAX_ZONES); ?></td>
                    <td class="smallText" align="right"><?php echo $zones_split->display_links($zones_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, MAX_DISPLAY_PAGE_LINKS, $_GET['zpage'], '', 'zpage'); ?></td>
                  </tr>
                </table></td>
              </tr>
              <tr>
                <td align="right" colspan="2"><?php if (!$_GET['action']) echo '<a href="' . xarModURL('commerce','admin',(FILENAME_GEO_ZONES, 'zpage=' . $_GET['zpage'] . '&zID=' . $zInfo->geo_zone_id . '&action=new_zone') . '">' . xarModAPIFunc('commerce','user','image',array('src' => xarTplGetImage('buttons/' . xarSessionGetVar('language') . '/'.'button_insert.gif'),'alt' => IMAGE_INSERT); . '</a>'; ?></td>
              </tr>
            </table>
<?php
  }
?>
            </td>
<?php
  $heading = array();
  $contents = array();

  if ($_GET['action'] == 'list') {
    switch ($_GET['saction']) {
      case 'new':
        $heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_NEW_SUB_ZONE . '</b>');

        $contents = array('form' => xtc_draw_form('zones', FILENAME_GEO_ZONES, 'zpage=' . $_GET['zpage'] . '&zID=' . $_GET['zID'] . '&action=list&spage=' . $_GET['spage'] . '&sID=' . $_GET['sID'] . '&saction=insert_sub'));
        $contents[] = array('text' => TEXT_INFO_NEW_SUB_ZONE_INTRO);
        $contents[] = array('text' => '<br>' . TEXT_INFO_COUNTRY . '<br>' . commerce_userapi_draw_pull_down_menu('zone_country_id', xtc_get_countries(TEXT_ALL_COUNTRIES), '', 'onChange="update_zone(this.form);"'));
        $contents[] = array('text' => '<br>' . TEXT_INFO_COUNTRY_ZONE . '<br>' . commerce_userapi_draw_pull_down_menu('zone_id', xtc_prepare_country_zones_pull_down()));
        $contents[] = array('align' => 'center', 'text' => '<br>' . <input type="image" src="#xarTplGetImage('buttons/' . xarSessionGetVar('language') . '/'.'button_insert.gif')#" border="0" alt=IMAGE_INSERT> . ' <a href="' . xarModURL('commerce','admin',(FILENAME_GEO_ZONES, 'zpage=' . $_GET['zpage'] . '&zID=' . $_GET['zID'] . '&action=list&spage=' . $_GET['spage'] . '&sID=' . $_GET['sID']) . '">' . xarModAPIFunc('commerce','user','image',array('src' => xarTplGetImage('buttons/' . xarSessionGetVar('language') . '/'.'button_cancel.gif'),'alt' => IMAGE_CANCEL); . '</a>');
        break;

      case 'edit':
        $heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_EDIT_SUB_ZONE . '</b>');

        $contents = array('form' => xtc_draw_form('zones', FILENAME_GEO_ZONES, 'zpage=' . $_GET['zpage'] . '&zID=' . $_GET['zID'] . '&action=list&spage=' . $_GET['spage'] . '&sID=' . $sInfo->association_id . '&saction=save_sub'));
        $contents[] = array('text' => TEXT_INFO_EDIT_SUB_ZONE_INTRO);
        $contents[] = array('text' => '<br>' . TEXT_INFO_COUNTRY . '<br>' . commerce_userapi_draw_pull_down_menu('zone_country_id', xtc_get_countries(TEXT_ALL_COUNTRIES), $sInfo->zone_country_id, 'onChange="update_zone(this.form);"'));
        $contents[] = array('text' => '<br>' . TEXT_INFO_COUNTRY_ZONE . '<br>' . commerce_userapi_draw_pull_down_menu('zone_id', xtc_prepare_country_zones_pull_down($sInfo->zone_country_id), $sInfo->zone_id));
        $contents[] = array('align' => 'center', 'text' => '<br>' . <input type="image" src="#xarTplGetImage('buttons/' . xarSessionGetVar('language') . '/'.'button_update.gif')#" border="0" alt=IMAGE_UPDATE> . ' <a href="' . xarModURL('commerce','admin',(FILENAME_GEO_ZONES, 'zpage=' . $_GET['zpage'] . '&zID=' . $_GET['zID'] . '&action=list&spage=' . $_GET['spage'] . '&sID=' . $sInfo->association_id) . '">' . xarModAPIFunc('commerce','user','image',array('src' => xarTplGetImage('buttons/' . xarSessionGetVar('language') . '/'.'button_cancel.gif'),'alt' => IMAGE_CANCEL); . '</a>');
        break;

      case 'delete':
        $heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_DELETE_SUB_ZONE . '</b>');

        $contents = array('form' => xtc_draw_form('zones', FILENAME_GEO_ZONES, 'zpage=' . $_GET['zpage'] . '&zID=' . $_GET['zID'] . '&action=list&spage=' . $_GET['spage'] . '&sID=' . $sInfo->association_id . '&saction=deleteconfirm_sub'));
        $contents[] = array('text' => TEXT_INFO_DELETE_SUB_ZONE_INTRO);
        $contents[] = array('text' => '<br><b>' . $sInfo->countries_name . '</b>');
        $contents[] = array('align' => 'center', 'text' => '<br>' . <input type="image" src="#xarTplGetImage('buttons/' . xarSessionGetVar('language') . '/'.'button_delete.gif')#" border="0" alt=IMAGE_DELETE> . ' <a href="' . xarModURL('commerce','admin',(FILENAME_GEO_ZONES, 'zpage=' . $_GET['zpage'] . '&zID=' . $_GET['zID'] . '&action=list&spage=' . $_GET['spage'] . '&sID=' . $sInfo->association_id) . '">' . xarModAPIFunc('commerce','user','image',array('src' => xarTplGetImage('buttons/' . xarSessionGetVar('language') . '/'.'button_cancel.gif'),'alt' => IMAGE_CANCEL); . '</a>');
        break;

      default:
        if (is_object($sInfo)) {
          $heading[] = array('text' => '<b>' . $sInfo->countries_name . '</b>');

          $contents[] = array('align' => 'center', 'text' => '<a href="' . xarModURL('commerce','admin',(FILENAME_GEO_ZONES, 'zpage=' . $_GET['zpage'] . '&zID=' . $_GET['zID'] . '&action=list&spage=' . $_GET['spage'] . '&sID=' . $sInfo->association_id . '&saction=edit') . '">' . xarModAPIFunc('commerce','user','image',array('src' => xarTplGetImage('buttons/' . xarSessionGetVar('language') . '/'.'button_edit.gif'),'alt' => IMAGE_EDIT); . '</a> <a href="' . xarModURL('commerce','admin',(FILENAME_GEO_ZONES, 'zpage=' . $_GET['zpage'] . '&zID=' . $_GET['zID'] . '&action=list&spage=' . $_GET['spage'] . '&sID=' . $sInfo->association_id . '&saction=delete') . '">' . xarModAPIFunc('commerce','user','image',array('src' => xarTplGetImage('buttons/' . xarSessionGetVar('language') . '/'.'button_delete.gif'),'alt' => IMAGE_DELETE); . '</a>');
          $contents[] = array('text' => '<br>' . TEXT_INFO_DATE_ADDED . ' ' . xarModAPIFunc('commerce','user','date_short',array('raw_date' =>$sInfo->date_added)));
          if (xarModAPIFunc('commerce','user','not_null',array('arg' => $sInfo->last_modified))) $contents[] = array('text' => TEXT_INFO_LAST_MODIFIED . ' ' . xarModAPIFunc('commerce','user','date_short',array('raw_date' =>$sInfo->last_modified)));
        }
        break;
    }
  } else {
    switch ($_GET['action']) {
      case 'new_zone':
        $heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_NEW_ZONE . '</b>');

        $contents = array('form' => xtc_draw_form('zones', FILENAME_GEO_ZONES, 'zpage=' . $_GET['zpage'] . '&zID=' . $_GET['zID'] . '&action=insert_zone'));
        $contents[] = array('text' => TEXT_INFO_NEW_ZONE_INTRO);
        $contents[] = array('text' => '<br>' . TEXT_INFO_ZONE_NAME . '<br>' . xtc_draw_input_field('geo_zone_name'));
        $contents[] = array('text' => '<br>' . TEXT_INFO_ZONE_DESCRIPTION . '<br>' . xtc_draw_input_field('geo_zone_description'));
        $contents[] = array('align' => 'center', 'text' => '<br>' . <input type="image" src="#xarTplGetImage('buttons/' . xarSessionGetVar('language') . '/'.'button_insert.gif')#" border="0" alt=IMAGE_INSERT> . ' <a href="' . xarModURL('commerce','admin',(FILENAME_GEO_ZONES, 'zpage=' . $_GET['zpage'] . '&zID=' . $_GET['zID']) . '">' . xarModAPIFunc('commerce','user','image',array('src' => xarTplGetImage('buttons/' . xarSessionGetVar('language') . '/'.'button_cancel.gif'),'alt' => IMAGE_CANCEL); . '</a>');
        break;

      case 'edit_zone':
        $heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_EDIT_ZONE . '</b>');

        $contents = array('form' => xtc_draw_form('zones', FILENAME_GEO_ZONES, 'zpage=' . $_GET['zpage'] . '&zID=' . $zInfo->geo_zone_id . '&action=save_zone'));
        $contents[] = array('text' => TEXT_INFO_EDIT_ZONE_INTRO);
        $contents[] = array('text' => '<br>' . TEXT_INFO_ZONE_NAME . '<br>' . xtc_draw_input_field('geo_zone_name', $zInfo->geo_zone_name));
        $contents[] = array('text' => '<br>' . TEXT_INFO_ZONE_DESCRIPTION . '<br>' . xtc_draw_input_field('geo_zone_description', $zInfo->geo_zone_description));
        $contents[] = array('align' => 'center', 'text' => '<br>' . <input type="image" src="#xarTplGetImage('buttons/' . xarSessionGetVar('language') . '/'.'button_update.gif')#" border="0" alt=IMAGE_UPDATE> . ' <a href="' . xarModURL('commerce','admin',(FILENAME_GEO_ZONES, 'zpage=' . $_GET['zpage'] . '&zID=' . $zInfo->geo_zone_id) . '">' . xarModAPIFunc('commerce','user','image',array('src' => xarTplGetImage('buttons/' . xarSessionGetVar('language') . '/'.'button_cancel.gif'),'alt' => IMAGE_CANCEL); . '</a>');
        break;

      case 'delete_zone':
        $heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_DELETE_ZONE . '</b>');

        $contents = array('form' => xtc_draw_form('zones', FILENAME_GEO_ZONES, 'zpage=' . $_GET['zpage'] . '&zID=' . $zInfo->geo_zone_id . '&action=deleteconfirm_zone'));
        $contents[] = array('text' => TEXT_INFO_DELETE_ZONE_INTRO);
        $contents[] = array('text' => '<br><b>' . $zInfo->geo_zone_name . '</b>');
        $contents[] = array('align' => 'center', 'text' => '<br>' . <input type="image" src="#xarTplGetImage('buttons/' . xarSessionGetVar('language') . '/'.'button_delete.gif')#" border="0" alt=IMAGE_DELETE> . ' <a href="' . xarModURL('commerce','admin',(FILENAME_GEO_ZONES, 'zpage=' . $_GET['zpage'] . '&zID=' . $zInfo->geo_zone_id) . '">' . xarModAPIFunc('commerce','user','image',array('src' => xarTplGetImage('buttons/' . xarSessionGetVar('language') . '/'.'button_cancel.gif'),'alt' => IMAGE_CANCEL); . '</a>');
        break;

      default:
        if (is_object($zInfo)) {
          $heading[] = array('text' => '<b>' . $zInfo->geo_zone_name . '</b>');

          $contents[] = array('align' => 'center', 'text' => '<a href="' . xarModURL('commerce','admin',(FILENAME_GEO_ZONES, 'zpage=' . $_GET['zpage'] . '&zID=' . $zInfo->geo_zone_id . '&action=edit_zone') . '">' . xarModAPIFunc('commerce','user','image',array('src' => xarTplGetImage('buttons/' . xarSessionGetVar('language') . '/'.'button_edit.gif'),'alt' => IMAGE_EDIT); . '</a> <a href="' . xarModURL('commerce','admin',(FILENAME_GEO_ZONES, 'zpage=' . $_GET['zpage'] . '&zID=' . $zInfo->geo_zone_id . '&action=delete_zone') . '">' . xarModAPIFunc('commerce','user','image',array('src' => xarTplGetImage('buttons/' . xarSessionGetVar('language') . '/'.'button_delete.gif'),'alt' => IMAGE_DELETE); . '</a>' . ' <a href="' . xarModURL('commerce','admin',(FILENAME_GEO_ZONES, 'zpage=' . $_GET['zpage'] . '&zID=' . $zInfo->geo_zone_id . '&action=list') . '">' .
        xarModAPIFunc('commerce','user','image',array('src' => xarTplGetImage('buttons/' . xarSessionGetVar('language') . '/'.'button_details.gif'),'alt' => IMAGE_DETAILS);
          </a>');
          $contents[] = array('text' => '<br>' . TEXT_INFO_NUMBER_ZONES . ' ' . $zInfo->num_zones);
          $contents[] = array('text' => '<br>' . TEXT_INFO_DATE_ADDED . ' ' . xarModAPIFunc('commerce','user','date_short',array('raw_date' =>$zInfo->date_added)));
          if (xarModAPIFunc('commerce','user','not_null',array('arg' => $zInfo->last_modified))) $contents[] = array('text' => TEXT_INFO_LAST_MODIFIED . ' ' . xarModAPIFunc('commerce','user','date_short',array('raw_date' =>$zInfo->last_modified)));
          $contents[] = array('text' => '<br>' . TEXT_INFO_ZONE_DESCRIPTION . '<br>' . $zInfo->geo_zone_description);
        }
        break;
    }
  }

  if ( (xarModAPIFunc('commerce','user','not_null',array('arg' => $heading))) && (xarModAPIFunc('commerce','user','not_null',array('arg' => $contents))) ) {
    echo '            <td width="25%" valign="top">' . "\n";

    $box = new box;
    echo $box->infoBox($heading, $contents);

    echo '            </td>' . "\n";
  }
}
?>
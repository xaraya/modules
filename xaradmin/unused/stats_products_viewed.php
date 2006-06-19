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

function commerce_admin_stats_product_viewed()
{

  if ($_GET['page'] > 1) $rows = $_GET['page'] * MAX_DISPLAY_SEARCH_RESULTS - MAX_DISPLAY_SEARCH_RESULTS;
  $product_query_raw = "select p.product_id, pd.product_name, pd.product_viewed, l.name from " . TABLE_PRODUCTS . " p, " . TABLE_product_DESCRIPTION . " pd, " . TABLE_LANGUAGES . " l where p.product_id = pd.product_id and l.languages_id = pd.language_id order by pd.product_viewed DESC";
  $product_split = new splitPageResults($_GET['page'], MAX_DISPLAY_SEARCH_RESULTS, $product_query_raw, $product_query_numrows);
  $product_query = new xenQuery($product_query_raw);
      $q = new xenQuery();
      if(!$q->run()) return;
  while ($products = $q->output()) {
    $rows++;

    if (strlen($rows) < 2) {
      $rows = '0' . $rows;
    }
?>
              <tr class="dataTableRow" onmouseover="this.className='dataTableRowOver';this.style.cursor='hand'" onmouseout="this.className='dataTableRow'" onclick="document.location.href='<?php echo xarModURL('commerce','admin',(FILENAME_CATEGORIES, 'action=new_product_preview&read=only&pID=' . $products['product_id'] . '&origin=' . FILENAME_STATS_product_VIEWED . '?page=' . $_GET['page'], 'NONSSL'); ?>'">
                <td class="dataTableContent"><?php echo $rows; ?>.</td>
                <td class="dataTableContent"><?php echo '<a href="' . xarModURL('commerce','admin',(FILENAME_CATEGORIES, 'action=new_product_preview&read=only&pID=' . $products['product_id'] . '&origin=' . FILENAME_STATS_product_VIEWED . '?page=' . $_GET['page'], 'NONSSL') . '">' . $products['product_name'] . '</a> (' . $products['name'] . ')'; ?></td>
                <td class="dataTableContent" align="center"><?php echo $products['product_viewed']; ?>&nbsp;</td>
              </tr>
<?php
  }
}
?>
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

function commerce_admin_stats_product_purchased()
{

  if ($_GET['page'] > 1) $rows = $_GET['page'] * MAX_DISPLAY_SEARCH_RESULTS - MAX_DISPLAY_SEARCH_RESULTS;
  $product_query_raw = "select p.product_id, p.product_ordered, pd.product_name from " . TABLE_PRODUCTS . " p, " . TABLE_product_DESCRIPTION . " pd where pd.product_id = p.product_id and pd.language_id = '" . $_SESSION['languages_id'] . "' and p.product_ordered > 0 group by pd.product_id order by p.product_ordered DESC, pd.product_name";
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
              <tr class="dataTableRow" onmouseover="this.className='dataTableRowOver';this.style.cursor='hand'" onmouseout="this.className='dataTableRow'" onclick="document.location.href='<?php echo xarModURL('commerce','admin',(FILENAME_CATEGORIES, 'action=new_product_preview&read=only&pID=' . $products['product_id'] . '&origin=' . FILENAME_STATS_product_PURCHASED . '?page=' . $_GET['page'], 'NONSSL'); ?>'">
                <td class="dataTableContent"><?php echo $rows; ?>.</td>
                <td class="dataTableContent"><?php echo '<a href="' . xarModURL('commerce','admin',(FILENAME_CATEGORIES, 'action=new_product_preview&read=only&pID=' . $products['product_id'] . '&origin=' . FILENAME_STATS_product_PURCHASED . '?page=' . $_GET['page'], 'NONSSL') . '">' . $products['product_name'] . '</a>'; ?></td>
                <td class="dataTableContent" align="center"><?php echo $products['product_ordered']; ?>&#160;</td>
              </tr>
<?php
  }
}
?>
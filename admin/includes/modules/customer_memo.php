<?php
/* --------------------------------------------------------------
   $Id: customer_memo.php,v 1.1 2003/09/06 22:05:29 fanta2k Exp $

   XT-Commerce - community made shopping
   http://www.xt-commerce.com

   Copyright (c) 2003 XT-Commerce

   (c) programmed by Zanier Mario for neXTCommerce

   Released under the GNU General Public License
   --------------------------------------------------------------
   based on:
   (c) 2003  nextcommerce (customer_memo.php,v 1.6 2003/08/18); www.nextcommerce.org

   --------------------------------------------------------------*/
?>
    <td valign="top" class="main"><?php echo ENTRY_MEMO; ?></td>
    <td class="main"><?php
  $memo_query = new xenQuery("SELECT
                                  *
                              FROM
                                  " . TABLE_CUSTOMERS_MEMO . "
                              WHERE
                                  customers_id = '" . $_GET['cID'] . "'
                              ORDER BY
                                  memo_date DESC");
      $q = new xenQuery();
      if(!$q->run()) return;
  while ($memo_values = $q->output()) {
    $poster_query = new xenQuery("SELECT customers_firstname, customers_lastname FROM " . TABLE_CUSTOMERS . " WHERE customers_id = '" . $memo_values['poster_id'] . "'");
      $q = new xenQuery();
      if(!$q->run()) return;
    $poster_values = $q->output();
?><table width="100%">
      <tr>
        <td class="main"><b><?php echo TEXT_DATE; ?></b>:<i><?php echo $memo_values['memo_date']; ?></i><b><?php echo TEXT_TITLE; ?></b>:<?php echo $memo_values['memo_title']; ?><b>  <?php echo TEXT_POSTER; ?></b>:<?php echo $poster_values['customers_lastname']; ?> <?php echo $poster_values['customers_firstname']; ?></td>
      </tr>
      <tr>
        <td width="142" class="main" style="border: 1px solid; border-color: #cccccc;"><?php echo $memo_values['memo_text']; ?></td>
      </tr>
      <tr>
        <td><a href="<?php echo xarModURL('commerce','admin',(FILENAME_CUSTOMERS, 'cID=' . $_GET['cID'] . '&action=edit&special=remove_memo&mID=' . $memo_values['memo_id']); ?>" onClick="return confirm('<?php echo DELETE_ENTRY; ?>')"><?php echo xarModAPIFunc('commerce','user','image',array('src' => xarTplGetImage('buttons/' . xarSession::getVar('language') . '/'.'button_delete.gif'),'alt' => IMAGE_DELETE);; ?></a></td>
      </tr>
    </table>
<?php
  }
?>
    <table width="100%">
      <tr>
        <td class="main" style="border-top: 1px solid; border-color: #cccccc;"><b><?php echo TEXT_TITLE ?></b>:<?php echo xtc_draw_input_field('memo_title'); ?><br><?php echo xtc_draw_textarea_field('memo_text', 'soft', '80', '5'); ?><br><?php echo <input type="image" src="#xarTplGetImage('buttons/' . xarSession::getVar('language') . '/'.'button_insert.gif')#" border="0" alt=IMAGE_INSERT>; ?></td>
      </tr>
    </table></td>

<?php
/* --------------------------------------------------------------
   $Id: mail.php,v 1.1 2003/09/06 22:05:29 fanta2k Exp $

   XT-Commerce - community made shopping
   http://www.xt-commerce.com

   Copyright (c) 2003 XT-Commerce
   --------------------------------------------------------------
   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(mail.php,v 1.30 2002/03/16 01:07:28); www.oscommerce.com
   (c) 2003  nextcommerce (mail.php,v 1.11 2003/08/18); www.nextcommerce.org

   Released under the GNU General Public License
   --------------------------------------------------------------
   Third Party contribution:
   Customers Status v3.x  (c) 2002-2003 Copyright Elari elari@free.fr | www.unlockgsm.com/dload-osc/ | CVS : http://cvs.sourceforge.net/cgi-bin/viewcvs.cgi/elari/?sortby=date#dirlist

   Released under the GNU General Public License
   --------------------------------------------------------------*/

  require('includes/application_top.php');

  if ( ($_GET['action'] == 'send_email_to_user') && ($_POST['customers_email_address']) && (!$_POST['back_x']) ) {
    switch ($_POST['customers_email_address']) {
      case '***':
        $mail_query = new xenQuery("select customers_firstname, customers_lastname, customers_email_address from " . TABLE_CUSTOMERS);
        $mail_sent_to = TEXT_ALL_CUSTOMERS;
        break;

      case '**D':
        $mail_query = new xenQuery("select customers_firstname, customers_lastname, customers_email_address from " . TABLE_CUSTOMERS . " where customers_newsletter = '1'");
        $mail_sent_to = TEXT_NEWSLETTER_CUSTOMERS;
        break;

      default:
        if (is_numeric($_POST['customers_email_address'])) {
          $mail_query = new xenQuery("select customers_firstname, customers_lastname, customers_email_address from " . TABLE_CUSTOMERS . " where customers_status = " . $_POST['customers_email_address']);
          $sent_to_query = new xenQuery("select customers_status_name from " . TABLE_CUSTOMERS_STATUS . " WHERE customers_status_id = '" . $_POST['customers_email_address'] . "' AND language_id='" . $_SESSION['languages_id'] . "'");
      $q = new xenQuery();
      if(!$q->run()) return;
          $sent_to = $q->output();
          $mail_sent_to = $sent_to['customers_status_name'];
        } else {
          $customers_email_address = xtc_db_prepare_input($_POST['customers_email_address']);
          $mail_query = new xenQuery("select customers_firstname, customers_lastname, customers_email_address from " . TABLE_CUSTOMERS . " where customers_email_address = '" . xtc_db_input($customers_email_address) . "'");
          $mail_sent_to = $_POST['customers_email_address'];
        }
        break;
    }

    $from = xtc_db_prepare_input($_POST['from']);
    $subject = xtc_db_prepare_input($_POST['subject']);
    $message = xtc_db_prepare_input($_POST['message']);

    //Let's build a message object using the email class
    $mimemessage = new email(array('X-Mailer: neXTCommerce bulk mailer'));
    // add the message to the object
    $mimemessage->add_text($message);
    $mimemessage->build_message();
      $q = new xenQuery();
      if(!$q->run()) return;
    while ($mail = $q->output()) {
      $mimemessage->send($mail['customers_firstname'] . ' ' . $mail['customers_lastname'], $mail['customers_email_address'], '', $from, $subject);
    }

    xarRedirectResponse(xarModURL('commerce','admin',(FILENAME_MAIL, 'mail_sent_to=' . urlencode($mail_sent_to)));
  }

  if ( ($_GET['action'] == 'preview') && (!$_POST['customers_email_address']) ) {
    $messageStack->add(ERROR_NO_CUSTOMER_SELECTED, 'error');
  }

  if ($_GET['mail_sent_to']) {
    $messageStack->add(sprintf(NOTICE_EMAIL_SENT_TO, $_GET['mail_sent_to']), 'notice');
  }
?>
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="Content-Type" content="<?php echo $_SESSION['language_charset']; ?>">
<title><?php echo TITLE; ?></title>
<link rel="stylesheet" type="text/css" href="includes/stylesheet.css">
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
        <td width="100%"><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td class="pageHeading"><?php echo HEADING_TITLE; ?></td>
            <td class="pageHeading" align="right"><?php echo xtc_draw_separator('pixel_trans.gif', HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT); ?></td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="2">
<?php
  if ( ($_GET['action'] == 'preview') && ($_POST['customers_email_address']) ) {
    switch ($_POST['customers_email_address']) {
      case '***':
        $mail_sent_to = TEXT_ALL_CUSTOMERS;
        break;

      case '**D':
        $mail_sent_to = TEXT_NEWSLETTER_CUSTOMERS;
        break;

      default:
        if (is_numeric($_POST['customers_email_address'])) {
          echo "hier bin ich";
          $sent_to_query = new xenQuery("select customers_status_name from " . TABLE_CUSTOMERS_STATUS . " WHERE customers_status_id = '" . $_POST['customers_email_address'] . "' AND language_id='" . $_SESSION['languages_id'] . "'");
      $q = new xenQuery();
      if(!$q->run()) return;
          $sent_to = $q->output();
          $mail_sent_to = $sent_to['customers_status_name'];
        } else {
          $mail_sent_to = $_POST['customers_email_address'];
        }
        break;
    }
?>
          <tr><?php echo xtc_draw_form('mail', FILENAME_MAIL, 'action=send_email_to_user'); ?>
            <td><table border="0" width="100%" cellpadding="0" cellspacing="2">
              <tr>
                <td><?php echo xtc_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
              </tr>
              <tr>
                <td class="smallText"><b><?php echo TEXT_CUSTOMER; ?></b><br><?php echo $mail_sent_to; ?></td>
              </tr>
              <tr>
                <td><?php echo xtc_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
              </tr>
              <tr>
                <td class="smallText"><b><?php echo TEXT_FROM; ?></b><br><?php echo htmlspecialchars(stripslashes($_POST['from'])); ?></td>
              </tr>
              <tr>
                <td><?php echo xtc_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
              </tr>
              <tr>
                <td class="smallText"><b><?php echo TEXT_SUBJECT; ?></b><br><?php echo htmlspecialchars(stripslashes($_POST['subject'])); ?></td>
              </tr>
              <tr>
                <td><?php echo xtc_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
              </tr>
              <tr>
                <td class="smallText"><b><?php echo TEXT_MESSAGE; ?></b><br><?php echo nl2br(htmlspecialchars(stripslashes($_POST['message']))); ?></td>
              </tr>
              <tr>
                <td><?php echo xtc_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
              </tr>
              <tr>
                <td><?php
    // Re-Post all POST'ed variables
    reset($_POST);
    while (list($key, $value) = each($_POST)) {
      if (!is_array($_POST[$key])) {
  <input type="hidden" name=#$key# value="#htmlspecialchars(stripslashes($value))#">
      }
    }
?>
                <table border="0" width="100%" cellpadding="0" cellspacing="2">
                  <tr>
                    <td>
    <input type="image" src="#xarTplGetImage('buttons/' . xarSession::getVar('language') . '/'.'button_back.gif')#" border="0" alt=IMAGE_BACK>
                    </td>
                    <td align="right"><?php echo '<a href="' . xarModURL('commerce','admin',(FILENAME_MAIL) . '">' . xarModAPIFunc('commerce','user','image',array('src' => xarTplGetImage('buttons/' . xarSession::getVar('language') . '/'.'button_cancel.gif'),'alt' => IMAGE_CANCEL); . '</a> ' .
    <input type="image" src="#xarTplGetImage('buttons/' . xarSession::getVar('language') . '/'.'button_send_mail.gif')#" border="0" alt=IMAGE_SEND_EMAIL>
                    </td>
                  </tr>
                </table></td>
              </tr>
            </table></td>
          </form></tr>
<?php
  } else {
?>
          <tr><?php echo xtc_draw_form('mail', FILENAME_MAIL, 'action=preview'); ?>
            <td><table border="0" cellpadding="0" cellspacing="2">
              <tr>
                <td colspan="2"><?php echo xtc_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
              </tr>
<?php
    $customers = array();
    $customers[] = array('id' => '', 'text' => TEXT_SELECT_CUSTOMER);
    $customers[] = array('id' => '***', 'text' => TEXT_ALL_CUSTOMERS);
    $customers[] = array('id' => '**D', 'text' => TEXT_NEWSLETTER_CUSTOMERS);
    // Customers Status 1.x
//    $customers_statuses_array = xtc_get_customers_statuses();
    $customers_statuses_array = new xenQuery("select customers_status_id , customers_status_name from " . TABLE_CUSTOMERS_STATUS . " WHERE language_id='" . $_SESSION['languages_id'] . "' order by customers_status_name");
      $q = new xenQuery();
      if(!$q->run()) return;
    while ($customers_statuses_value = $q->output()) {
      $customers[] = array('id' => $customers_statuses_value['customers_status_id'],
                           'text' => $customers_statuses_value['customers_status_name']);
    }
    // End customers Status 1.x
    $mail_query = new xenQuery("select customers_email_address, customers_firstname, customers_lastname from " . TABLE_CUSTOMERS . " order by customers_lastname");
      $q = new xenQuery();
      if(!$q->run()) return;
    while($customers_values = $q->output()) {
      $customers[] = array('id' => $customers_values['customers_email_address'],
                           'text' => $customers_values['customers_lastname'] . ', ' . $customers_values['customers_firstname'] . ' (' . $customers_values['customers_email_address'] . ')');
    }
?>
              <tr>
                <td class="main"><?php echo TEXT_CUSTOMER; ?></td>
                <td><?php echo commerce_userapi_draw_pull_down_menu('customers_email_address', $customers, $_GET['customer']);?></td>
              </tr>
              <tr>
                <td colspan="2"><?php echo xtc_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
              </tr>
              <tr>
                <td class="main"><?php echo TEXT_FROM; ?></td>
                <td><?php echo xtc_draw_input_field('from', EMAIL_FROM); ?></td>
              </tr>
              <tr>
                <td colspan="2"><?php echo xtc_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
              </tr>
              <tr>
                <td class="main"><?php echo TEXT_SUBJECT; ?></td>
                <td><?php echo xtc_draw_input_field('subject'); ?></td>
              </tr>
              <tr>
                <td colspan="2"><?php echo xtc_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
              </tr>
              <tr>
                <td valign="top" class="main"><?php echo TEXT_MESSAGE; ?></td>
                <td><?php echo xtc_draw_textarea_field('message', 'soft', '60', '15'); ?></td>
              </tr>
              <tr>
                <td colspan="2"><?php echo xtc_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
              </tr>
              <tr>
                <td colspan="2" align="right">
    <input type="image" src="#xarTplGetImage('buttons/' . xarSession::getVar('language') . '/'.'button_send_mail.gif')#" border="0" alt=IMAGE_SEND_EMAIL>
                </td>
              </tr>
            </table></td>
          </form></tr>
<?php
  }
?>
<!-- body_text_eof //-->
        </table></td>
      </tr>
    </table></td>
  </tr>
</table>
<!-- body_eof //-->

<!-- footer //-->
<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
<!-- footer_eof //-->
<br>
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
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

  include( 'includes/application_top.php');
//      $smarty = new Smarty;
  // include boxes
  require(DIR_WS_INCLUDES.'boxes.php');
  // include needed functions
  require_once(DIR_FS_INC . 'xtc_draw_textarea_field.inc.php');
  require_once(DIR_WS_CLASSES.'class.phpmailer.php');
  require_once(DIR_FS_INC . 'xtc_php_mail.inc.php');

  if (isset($_SESSION['customer_id'])) {
    $account = new xenQuery("select customers_firstname, customers_lastname, customers_email_address from " . TABLE_CUSTOMERS . " where customers_id = '" . $_SESSION['customer_id'] . "'");
      $q = new xenQuery();
      if(!$q->run()) return;
    $account_values = $q->output();
  } elseif (ALLOW_GUEST_TO_TELL_A_FRIEND == 'false') {
  //
    xarRedirectResponse(xarModURL('commerce','user','login', '', 'SSL'));
  }

  $valid_product = false;
  if (isset($_GET['products_id'])) {
    $product_info_query = new xenQuery("select pd.products_name from " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_DESCRIPTION . " pd where p.products_status = '1' and p.products_id = '" . (int)$_GET['products_id'] . "' and p.products_id = pd.products_id and pd.language_id = '" . $_SESSION['languages_id'] . "'");
    $valid_product = ($product_info_query->getrows() > 0);
  }



  $breadcrumb->add(NAVBAR_TITLE_TELL_A_FRIEND, xarModURL('commerce','user','tell_a_friend', array('send_to' =>$_GET['send_to'], 'products_id' => $_GET['products_id']));

 require(DIR_WS_INCLUDES . 'header.php');

  if ($valid_product == false) {
?>
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td class="pageHeading"><?php echo HEADING_TITLE_ERROR_TELL_A_FRIEND; ?></td>
          </tr>
          <tr>
            <td><?php echo xtc_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
          </tr>
          <tr>
            <td class="main"><?php echo ERROR_INVALID_PRODUCT_TELL_A_FRIEND; ?></td>
          </tr>
        </table></td>
      </tr>
<?php
  } else {
      $q = new xenQuery();
      if(!$q->run()) return;
    $product_info = $q->output();
    $data['heading_tell_a_friend'] = sprintf(HEADING_TITLE_TELL_A_FRIEND, $product_info['products_name']);

    $error = false;

    if (isset($_GET['action']) && ($_GET['action'] == 'process') && !xarModAPIFunc('commerce','user','validate_email',array('email' =>trim($_POST['friendemail'])))) {
      $friendemail_error = true;
      $error = true;
    } else {
      $friendemail_error = false;
    }

    if (isset($_GET['action']) && ($_GET['action'] == 'process') && empty($_POST['friendname'])) {
      $friendname_error = true;
      $error = true;
    } else {
      $friendname_error = false;
    }

    if (isset($_SESSION['customer_id'])) {
      $from_name = $account_values['customers_firstname'] . ' ' . $account_values['customers_lastname'];
      $from_email_address = $account_values['customers_email_address'];
    } else {
      $from_name = $_POST['yourname'];
      $from_email_address = $_POST['from'];
    }

    if (!isset($_SESSION['customer_id'])) {
      if (isset($_GET['action']) && ($_GET['action'] == 'process') && !xarModAPIFunc('commerce','user','validate_email',array('email' =>trim($from_email_address)))) {
        $fromemail_error = true;
        $error = true;
      } else {
        $fromemail_error = false;
      }
    }

    if (isset($_GET['action']) && ($_GET['action'] == 'process') && empty($from_name)) {
      $fromname_error = true;
      $error = true;
    } else {
      $fromname_error = false;
    }

    if (isset($_GET['action']) && ($_GET['action'] == 'process') && ($error == false)) {
      $email_subject = sprintf(TEXT_EMAIL_SUBJECT, $from_name, STORE_NAME);
      $email_body = sprintf(TEXT_EMAIL_INTRO, $_POST['friendname'], $from_name, $_POST['products_name'], STORE_NAME) . "\n\n";

      if (xarModAPIFunc('commerce','user','not_null',array('arg' => $_POST['yourmessage']))) {
        $email_body .= $_POST['yourmessage'] . "\n\n";
      }

      $email_body .= sprintf(TEXT_EMAIL_LINK, xarModURL('commerce','user','product_info', 'products_id=' . $_GET['products_id'])) . "\n\n" .
                     sprintf(TEXT_EMAIL_SIGNATURE, STORE_NAME . "\n" . HTTP_SERVER . DIR_WS_CATALOG . "\n");

      xtc_php_mail($from_email_address, $from_name,$_POST['friendemail'],$_POST['friendname'], '', $from_email_address, $from_name, '', '', $email_subject, htmlspecialchars($email_body) , htmlspecialchars($email_body) );
       $data['action'] = 'send';
       $data['message'] = sprintf(TEXT_EMAIL_SUCCESSFUL_SENT, stripslashes($_POST['products_name']), $_POST['friendemail']);
       $data['BUTTON_CONTINUE'] = '<a href="' . xarModURL('commerce','user','product_info', 'products_id=' . $_GET['products_id']) . '">' .
xarModAPIFunc('commerce','user','image',array('src' => xarTplGetImage('buttons/' . xarSessionGetVar('language') . '/'.'button_continue.gif'),
        'alt' => IMAGE_BUTTON_CONTINUE);
       . '</a>';

    } else {
      if (isset($_SESSION['customer_id'])) {
        $your_name_prompt = $account_values['customers_firstname'] . ' ' . $account_values['customers_lastname'];
        $your_email_address_prompt = $account_values['customers_email_address'];
      } else {
        $your_name_prompt = xtc_draw_input_field('yourname', (($fromname_error == true) ? $_POST['yourname'] : $_GET['yourname']));
        if ($fromname_error == true) $your_name_prompt .= '&#160;' . TEXT_REQUIRED;
        $your_email_address_prompt = xtc_draw_input_field('from', (($fromemail_error == true) ? $_POST['from'] : $_GET['from']));
        if ($fromemail_error == true) $your_email_address_prompt .= ENTRY_EMAIL_ADDRESS_CHECK_ERROR;
      }

    xtc_draw_hidden_field('products_name', $product_info['products_name']);
$data['INPUT_NAME'] = $your_name_prompt;
$data['INPUT_EMAIL'] = $your_email_address_prompt;
$data['INPUT_MESSAGE'] = xtc_draw_textarea_field('yourmessage', 'soft', 40, 8;

$input_friendname= xtc_draw_input_field('friendname', (($friendname_error == true) ? $_POST['friendname'] : $_GET['friendname']));
 if ($friendname_error == true) $input_friendname.= '&#160;' . TEXT_REQUIRED;

$input_friendemail= xtc_draw_input_field('friendemail', (($friendemail_error == true) ? $_POST['friendemail'] : $_GET['send_to']));
if ($friendemail_error == true) $input_friendemail.= ENTRY_EMAIL_ADDRESS_CHECK_ERROR;
$data['INPUT_FRIENDNAME'] = $input_friendname;
$data['INPUT_FRIENDEMAIL'] = $input_friendemail;

$data['BUTTON_BACK'] = '<a href="' . xarModURL('commerce','user','product_info', 'products_id=' . $_GET['products_id']) . '">' .
xarModAPIFunc('commerce','user','image',array('src' => xarTplGetImage('buttons/' . xarSessionGetVar('language') . '/'.'button_back.gif'),
        'alt' => IMAGE_BUTTON_BACK);
. '</a>';
$data['BUTTON_SUBMIT'] =
<input type="image" src="#xarTplGetImage('buttons/' . xarSessionGetVar('language') . '/'.'button_continue.gif')#" border="0" alt=IMAGE_BUTTON_CONTINUE>
<input type="image" src="#xarTplGetImage('buttons/' . xarSessionGetVar('language') . '/'.'button_continue.gif')#" border="0" alt=IMAGE_BUTTON_CONTINUE>;
    }
  }

  $data['language'] =  $_SESSION['language'];


               // set cache ID
  if (USE_CACHE=='false') {
  $smarty->caching = 0;
  return data;
  } else {
  $smarty->caching = 1;
  $smarty->cache_lifetime=CACHE_LIFETIME;
  $smarty->cache_modified_check=CACHE_CHECK;
  $cache_id = $_SESSION['language'];
  return data;
  }
  ?>
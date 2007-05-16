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

//    $smarty = new Smarty;
  // include boxes
  require(DIR_WS_INCLUDES.'boxes.php');

  // include needed functions
  require_once(DIR_FS_INC . 'xtc_draw_textarea_field.inc.php');
  require_once(DIR_WS_CLASSES.'class.phpmailer.php');
  require_once(DIR_FS_INC . 'xtc_php_mail.inc.php');

         $shop_content_query=new xenQuery("SELECT
                    content_title,
                    content_heading,
                    content_text,
                    content_file
                    FROM ".TABLE_CONTENT_MANAGER."
                    WHERE content_group='4'
                    AND languages_id='".$_SESSION['languages_id']."'");
      $q = new xenQuery();
      if(!$q->run()) return;
    $shop_content_data=$q->output();


  $error = false;
  if (isset($_GET['action']) && ($_GET['action'] == 'send')) {
    if (xarModAPIFunc('commerce','user','validate_email',array('email' =>trim($_POST['email'])))) {
      xtc_php_mail($_POST['email'], $_POST['name'], CONTACT_US_EMAIL_ADDRESS, CONTACT_US_NAME, CONTACT_US_FORWARDING_STRING, CONTACT_US_REPLY_ADDRESS, CONTACT_US_REPLY_ADDRESS_NAME, '', '', CONTACT_US_EMAIL_SUBJECT, nl2br($_POST['message_body']), $_POST['message_body']);
      if (!isset($mail_error)) {
          xarRedirectResponse(xarModURL('commerce','user',(FILENAME_CONTACT_US, 'action=success'));
      }
      else {
          echo $mail_error;
      }
    } else {
      $error = true;
    }


  }

  $breadcrumb->add(NAVBAR_TITLE_CONTACT_US, xarModURL('commerce','user','contact_us');
 require(DIR_WS_INCLUDES . 'header.php');
 $data['CONTACT_HEADING'] = $shop_content_data['content_title'];




  if (isset($_GET['action']) && ($_GET['action'] == 'success')) {
  $data['success'] = '1';
  $data['BUTTON_CONTINUE'] = '<a href="'.xarModURL('commerce','user','default').'">'.
  xarModAPIFunc('commerce','user','image',array('src' => xarTplGetImage('buttons/' . xarSession::getVar('language') . '/'.'button_continue.gif'),
        'alt' => IMAGE_BUTTON_CONTINUE);
.'</a>';

  } else {

 if ($shop_content_data['content_file']!=''){
if (strpos($shop_content_data['content_file'],'.txt')) echo '<pre>';
include(DIR_FS_CATALOG.'media/content/'.$shop_content_data['content_file']);
if (strpos($shop_content_data['content_file'],'.txt')) echo '</pre>';
 } else {
$contact_content= $shop_content_data['content_text'];
}
$data['CONTACT_CONTENT'] = $contact_content;

$data['INPUT_NAME'] = xtc_draw_input_field('name', ($error ? $_POST['name'] : $first_name));
$data['INPUT_EMAIL'] = xtc_draw_input_field('email', ($error ? $_POST['email'] : $email_address));
$data['INPUT_TEXT'] = xtc_draw_textarea_field('message_body', 'soft', 50, 15, $_POST['']);
$data['BUTTON_SUBMIT'] = <input type="image" src="#xarTplGetImage('buttons/' . xarSession::getVar('language') . '/'.'button_continue.gif')#" border="0" alt=IMAGE_BUTTON_CONTINUE>;
  }

  $data['language'] = $_SESSION['language'];


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

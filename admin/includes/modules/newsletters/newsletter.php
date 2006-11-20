<?php
/* --------------------------------------------------------------
   $Id: newsletter.php,v 1.1 2003/09/06 22:05:29 fanta2k Exp $

   XT-Commerce - community made shopping
   http://www.xt-commerce.com

   Copyright (c) 2003 XT-Commerce
   --------------------------------------------------------------
   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(newsletter.php,v 1.1 2002/03/08); www.oscommerce.com
   (c) 2003  nextcommerce (newsletter.php,v 1.7 2003/08/18); www.nextcommerce.org

   Released under the GNU General Public License
   --------------------------------------------------------------
   Third Party contribution:
   Customers Status v3.x  (c) 2002-2003 Copyright Elari elari@free.fr | www.unlockgsm.com/dload-osc/ | CVS : http://cvs.sourceforge.net/cgi-bin/viewcvs.cgi/elari/?sortby=date#dirlist

   Released under the GNU General Public License
   --------------------------------------------------------------*/

  class newsletter {
    var $show_choose_audience, $title, $content;

    function newsletter($title, $content) {
      $this->show_choose_audience = false;
      $this->title = $title;
      $this->content = $content;
    }

    function choose_audience() {
      global $customers_statuses_array ;


//      $products_array = array();
//      $products_query = new xenQuery("select pd.products_id, pd.products_name from " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_DESCRIPTION . " pd where pd.language_id = '" . $_SESSION['languages_id'] . "' and pd.products_id = p.products_id and p.products_status = '1' order by pd.products_name");
//      while ($products = $q->output()) {
//        $products_array[] = array('id' => $products['products_id'],
//                                  'text' => $products['products_name']);
//      }

$choose_audience_string = '<script language="javascript"><!--
function mover(move) {
  if (move == \'remove\') {
    for (x=0; x<(document.notifications.cstatuses.length); x++) {
      if (document.notifications.cstatuses.options[x].selected) {
        with(document.notifications.elements[\'chosen[]\']) {
          options[options.length] = new Option(document.notifications.cstatuses.options[x].text,document.notifications.cstatuses.options[x].value);
        }
        document.notifications.cstatuses.options[x] = null;
        x = -1;
      }
    }
  }
  if (move == \'add\') {
    for (x=0; x<(document.notifications.elements[\'chosen[]\'].length); x++) {
      if (document.notifications.elements[\'chosen[]\'].options[x].selected) {
        with(document.notifications.cstatuses) {
          options[options.length] = new Option(document.notifications.elements[\'chosen[]\'].options[x].text,document.notifications.elements[\'chosen[]\'].options[x].value);
        }
        document.notifications.elements[\'chosen[]\'].options[x] = null;
        x = -1;
      }
    }
  }
  return true;
}

function selectAll(FormName, SelectBox) {
  temp = "document." + FormName + ".elements[\'" + SelectBox + "\']";
  Source = eval(temp);

  for (x=0; x<(Source.length); x++) {
    Source.options[x].selected = "true";
  }

  if (x<1) {
    alert(\'' . JS_PLEASE_SELECT_PRODUCTS . '\');
    return false;
  } else {
    return true;
  }
}
//--></script>';


      $choose_audience_string .= '<form name="notifications" action="' . xarModURL('commerce','admin',(FILENAME_NEWSLETTERS, 'page=' . $_GET['page'] . '&nID=' . $_GET['nID'] . '&action=confirm') . '" method="post" onSubmit="return selectAll(\'notifications\', \'chosen[]\')"><table border="0" width="100%" cellspacing="0" cellpadding="2">' . "\n" .
                                 '  <tr>' . "\n" .
                                 '    <td align="center" class="main"><b>' . TEXT_CUSTOMERS_STATUS . '</b><br>' . commerce_userapi_draw_pull_down_menu('cstatuses', $customers_statuses_array, '', 'size="20" style="width: 20em;" multiple') . '</td>' . "\n" .
                                 '    <td align="center" class="main">&#160;<br><a href="' . xarModURL('commerce','admin',(FILENAME_NEWSLETTERS, 'page=' . $_GET['page'] . '&nID=' . $_GET['nID'] . '&action=confirm&global=true') . '"><input type="button" value="' . BUTTON_GLOBAL . '" style="width: 8em;"></a><br><br><br><input type="button" value="' . BUTTON_SELECT . '" style="width: 8em;" onClick="mover(\'remove\');"><br><br><input type="button" value="' . BUTTON_UNSELECT . '" style="width: 8em;" onClick="mover(\'add\');"><br><br><br><input type="submit" value="' . BUTTON_SUBMIT . '" style="width: 8em;"><br><br><a href="' . xarModURL('commerce','admin',(FILENAME_NEWSLETTERS, 'page=' . $_GET['page'] . '&nID=' . $_GET['nID']) . '"><input type="button" value="' . BUTTON_CANCEL . '" style="width: 8em;"></a></td>' . "\n" .
                                 '    <td align="center" class="main"><b>' . TEXT_SELECTED_CUSTOMERS_STATUS . '</b><br>' . commerce_userapi_draw_pull_down_menu('chosen[]', array(), '', 'size="20" style="width: 20em;" multiple') . '</td>' . "\n" .
                                 '  </tr>' . "\n" .
                                 '</table></form>';

      return $choose_audience_string;



      return $choose_audience_string;
    }

    function confirm() {

      $mail_query = new xenQuery("select count(*) as count from " . TABLE_CUSTOMERS . " where customers_newsletter = '1'");
      $q = new xenQuery();
      if(!$q->run()) return;
      $mail = $q->output();

      $confirm_string = '<table border="0" cellspacing="0" cellpadding="2">' . "\n" .
                        '  <tr>' . "\n" .
                        '    <td class="main"><font color="#ff0000"><b>' . sprintf(TEXT_COUNT_CUSTOMERS, $mail['count']) . '</b></font></td>' . "\n" .
                        '  </tr>' . "\n" .
                        '  <tr>' . "\n" .
                        '    <td>' . xtc_draw_separator('pixel_trans.gif', '1', '10') . '</td>' . "\n" .
                        '  </tr>' . "\n" .
                        '  <tr>' . "\n" .
                        '    <td class="main"><b>' . $this->title . '</b></td>' . "\n" .
                        '  </tr>' . "\n" .
                        '  <tr>' . "\n" .
                        '    <td>' . xtc_draw_separator('pixel_trans.gif', '1', '10') . '</td>' . "\n" .
                        '  </tr>' . "\n" .
                        '  <tr>' . "\n" .
                        '    <td class="main"><tt>' . nl2br($this->content) . '</tt></td>' . "\n" .
                        '  </tr>' . "\n" .
                        '  <tr>' . "\n" .
                        '    <td>' . xtc_draw_separator('pixel_trans.gif', '1', '10') . '</td>' . "\n" .
                        '  </tr>' . "\n" .
                        '  <tr>' . "\n" .
                        '    <td align="right"><a href="' . xarModURL('commerce','admin',(FILENAME_NEWSLETTERS, 'page=' . $_GET['page'] . '&nID=' . $_GET['nID'] . '&action=confirm_send') . '">' . xarModAPIFunc('commerce','user','image',array('src' => xarTplGetImage('buttons/' . xarSessionGetVar('language') . '/'.'button_send.gif'),'alt' => IMAGE_SEND); . '</a> <a href="' . xarModURL('commerce','admin',(FILENAME_NEWSLETTERS, 'page=' . $_GET['page'] . '&nID=' . $_GET['nID']) . '">' . xarModAPIFunc('commerce','user','image',array('src' => xarTplGetImage('buttons/' . xarSessionGetVar('language') . '/'.'button_cancel.gif'),'alt' => IMAGE_CANCEL); . '</a></td>' . "\n" .
                        '  </tr>' . "\n" .
                        '</table>';

      return $confirm_string;
    }

    function send($newsletter_id) {
      $mail_query = new xenQuery("select customers_firstname, customers_lastname, customers_email_address from " . TABLE_CUSTOMERS . " where customers_newsletter = '1'");

      $mimemessage = new email(array('X-Mailer: osCommerce bulk mailer'));
      $mimemessage->add_text($this->content);
      $mimemessage->build_message();
      $q = new xenQuery();
      if(!$q->run()) return;
      while ($mail = $q->output()) {
        $mimemessage->send($mail['customers_firstname'] . ' ' . $mail['customers_lastname'], $mail['customers_email_address'], '', EMAIL_FROM, $this->title);
      }

      $newsletter_id = xtc_db_prepare_input($newsletter_id);
      new xenQuery("update " . TABLE_NEWSLETTERS . " set date_sent = now(), status = '1' where newsletters_id = '" . xtc_db_input($newsletter_id) . "'");
    }
  }
?>
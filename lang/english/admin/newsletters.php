<?php
/* --------------------------------------------------------------
   $Id: newsletters.php,v 1.1 2003/12/19 13:19:08 fanta2k Exp $   

   XT-Commerce - community made shopping
   http://www.xt-commerce.com

   Copyright (c) 2003 XT-Commerce
   --------------------------------------------------------------
   based on: 
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(newsletters.php,v 1.7 2002/03/11); www.oscommerce.com 
   (c) 2003	 nextcommerce (newsletters.php,v 1.5 2003/08/14); www.nextcommerce.org

   Released under the GNU General Public License 
   --------------------------------------------------------------*/

define('HEADING_TITLE', 'Newsletter Manager');

define('TABLE_HEADING_NEWSLETTERS', 'Newsletters');
define('TABLE_HEADING_SIZE', 'Size');
define('TABLE_HEADING_MODULE', 'Module');
define('TABLE_HEADING_SENT', 'Sent');
define('TABLE_HEADING_STATUS', 'Status');
define('TABLE_HEADING_ACTION', 'Action');

define('TEXT_NEWSLETTER_MODULE', 'Module:');
define('TEXT_NEWSLETTER_TITLE', 'Newsletter Title:');
define('TEXT_NEWSLETTER_CONTENT', 'Content:');

define('TEXT_NEWSLETTER_DATE_ADDED', 'Date Added:');
define('TEXT_NEWSLETTER_DATE_SENT', 'Date Sent:');

define('TEXT_INFO_DELETE_INTRO', 'Are you sure you want to delete this newsletter?');

define('TEXT_PLEASE_WAIT', 'Please wait .. sending emails ..<br><br>Please do not interrupt this process!');
define('TEXT_FINISHED_SENDING_EMAILS', 'Finished sending e-mails!');

define('ERROR_NEWSLETTER_TITLE', 'Error: Newsletter title required');
define('ERROR_NEWSLETTER_MODULE', 'Error: Newsletter module required');
define('ERROR_REMOVE_UNLOCKED_NEWSLETTER', 'Error: Please lock the newsletter before deleting it.');
define('ERROR_EDIT_UNLOCKED_NEWSLETTER', 'Error: Please lock the newsletter before editing it.');
define('ERROR_SEND_UNLOCKED_NEWSLETTER', 'Error: Please lock the newsletter before sending it.');
define('TABLE_HEADING_NEWS_HIST_CS_VALUE','Changed');
define('TABLE_HEADING_NEWS_HIST_DATE_ADDED','Date');
define('TEXT_NO_NEWSLETTERS_CS_HISTORY','-no changes-');
?>

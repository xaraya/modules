<?php
/* --------------------------------------------------------------
   $Id: newsletters.php,v 1.1 2003/09/28 14:38:01 fanta2k Exp $   

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

define('HEADING_TITLE', 'Rundschreiben Verwaltung');

define('TABLE_HEADING_NEWSLETTERS', 'Rundschreiben');
define('TABLE_HEADING_SIZE', 'Gr&ouml;sse');
define('TABLE_HEADING_MODULE', 'Module');
define('TABLE_HEADING_SENT', 'Gesendet');
define('TABLE_HEADING_STATUS', 'Status');
define('TABLE_HEADING_ACTION', 'Aktion');

define('TEXT_NEWSLETTER_MODULE', 'Module:');
define('TEXT_NEWSLETTER_TITLE', 'Titel des Rundschreibens:');
define('TEXT_NEWSLETTER_CONTENT', 'Inhalt:');

define('TEXT_NEWSLETTER_DATE_ADDED', 'hinzugef&uuml;gt am:');
define('TEXT_NEWSLETTER_DATE_SENT', 'Datum gesendet:');

define('TEXT_INFO_DELETE_INTRO', 'Sind Sie sicher, dass Sie dieses Rundschreiben l&ouml;schen m&ouml;chten?');

define('TEXT_PLEASE_WAIT', 'Bitte warten Sie .. eMails werden gesendet ..<br><br>Bitte unterbrechen Sie diesen Prozess nicht!');
define('TEXT_FINISHED_SENDING_EMAILS', 'eMails wurden versendet!');

define('ERROR_NEWSLETTER_TITLE', 'Fehler: Ein Titel f&uuml;r das Rundschreiben ist erforderlich.');
define('ERROR_NEWSLETTER_MODULE', 'Fehler: Das Newsletter Modul wird ben&ouml;tigt.');
define('ERROR_REMOVE_UNLOCKED_NEWSLETTER', 'Fehler: Bitte sperren Sie das Rundschreiben bevor Sie es l&ouml;schen.');
define('ERROR_EDIT_UNLOCKED_NEWSLETTER', 'Fehler: Bitte sperren Sie das Rundschreiben bevor Sie es bearbeiten.');
define('ERROR_SEND_UNLOCKED_NEWSLETTER', 'Fehler: Bitte sperren Sie das Rundschreiben bevor Sie es versenden.');
define('TABLE_HEADING_NEWS_HIST_CS_VALUE','Änderung');
define('TABLE_HEADING_NEWS_HIST_DATE_ADDED','Datum');
define('TEXT_NO_NEWSLETTERS_CS_HISTORY','-keine Änderungen-');
?>
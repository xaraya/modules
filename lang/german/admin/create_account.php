<?php
/* --------------------------------------------------------------
   $Id: create_account.php,v 1.1 2003/09/28 14:38:01 fanta2k Exp $   

   XT-Commerce - community made shopping
   http://www.xt-commerce.com

   Copyright (c) 2003 XT-Commerce
   --------------------------------------------------------------
   based on: 
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(create_account.php,v 1.13 2003/05/19); www.oscommerce.com 
   (c) 2003	 nextcommerce (create_account.php,v 1.4 2003/08/14); www.nextcommerce.org

   Released under the GNU General Public License 
   --------------------------------------------------------------*/

define('NAVBAR_TITLE', 'Konto erstellen');

define('HEADING_TITLE', 'Admin Kundenkonto-Interface');

define('TEXT_ORIGIN_LOGIN', '<font color="#FF0000"><small><b>ACHTUNG:</b></font></small> Wenn Sie bereits ein Konto besitzen, so melden Sie sich bitte <a href="%s"><u><b>hier</b></u></a> an.');

define('EMAIL_SUBJECT', 'Willkommen zu ' . STORE_NAME);
define('EMAIL_GREET_MR', 'Sehr geehrter Herr ' . stripslashes($HTTP_POST_VARS['lastname']) . ',' . "\n\n");
define('EMAIL_GREET_MS', 'Sehr geehrte Frau ' . stripslashes($HTTP_POST_VARS['lastname']) . ',' . "\n\n");
define('EMAIL_GREET_NONE', 'Sehr geehrte ' . stripslashes($HTTP_POST_VARS['firstname']) . ',' . "\n\n");
define('EMAIL_WELCOME', 'willkommen zu <b>' . STORE_NAME . '</b>.' . "\n\n");
define('EMAIL_TEXT', 'Sie können jetzt unseren <b>Online-Service</b> nutzen. Der Service bietet unter anderem:' . "\n\n" . '<li><b>Kundenwarenkorb</b> - Jeder Artikel bleibt registriert bis Sie zur Kasse gehen, oder die Produkte aus dem Warenkorb entfernen.' . "\n" . '<li><b>Adressbuch</b> - Wir können jetzt die Produkte zu der von Ihnen ausgesuchten Adresse senden. Der perfekte Weg ein Geburtstagsgeschenk zu versenden.' . "\n" . '<li><b>Vorherige Bestellungen</b> - Sie können jederzeit Ihre vorherigen Bestellungen überprüfen.' . "\n" . '<li><b>Meinungen über Produkte</b> - Teilen Sie Ihre Meinung zu unseren Produkten mit anderen Kunden.' . "\n\n");
define('EMAIL_CONTACT', 'Falls Sie Fragen zu unserem Kunden-Service haben, wenden Sie sich bitte an den Vertrieb: ' . STORE_OWNER_EMAIL_ADDRESS . '.' . "\n\n");
define('EMAIL_WARNING', '<b>Achtung:</b> Diese eMail-Adresse wurde uns von einem Kunden bekannt gegeben. Falls Sie sich nicht angemeldet haben, senden Sie bitte eine eMail an ' . STORE_OWNER_EMAIL_ADDRESS . '.' . "\n");
?>
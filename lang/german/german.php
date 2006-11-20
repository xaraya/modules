<?php
/* -----------------------------------------------------------------------------------------
   $Id: german.php,v 1.1 2003/09/28 14:38:01 fanta2k Exp $

   XT-Commerce - community made shopping
   http://www.xt-commerce.com

   Copyright (c) 2003 XT-Commerce
   -----------------------------------------------------------------------------------------
   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(german.php,v 1.119 2003/05/19); www.oscommerce.com
   (c) 2003  nextcommerce (german.php,v 1.25 2003/08/25); www.nextcommerce.org

   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/

// look in your $PATH_LOCALE/locale directory for available locales..
// on RedHat try 'de_DE'
// on FreeBSD try 'de_DE.ISO_8859-15'
// on Windows try 'de' or 'German'
@setlocale(LC_TIME, 'de_DE.ISO_8859-15');
define('DATE_FORMAT_SHORT', '%d.%m.%Y');  // this is used for strftime()
define('DATE_FORMAT_LONG', '%A, %d. %B %Y'); // this is used for strftime()
define('DATE_FORMAT', 'd.m.Y');  // this is used for strftime()
define('DATE_TIME_FORMAT', DATE_FORMAT_SHORT . ' %H:%M:%S');

// page title
define('TITLE', 'XT-Commerce');

////
// Return date in raw format
// $date should be in format mm/dd/yyyy
// raw date is in format YYYYMMDD, or DDMMYYYY
function xtc_date_raw($date, $reverse = false) {
  if ($reverse) {
    return substr($date, 0, 2) . substr($date, 3, 2) . substr($date, 6, 4);
  } else {
    return substr($date, 6, 4) . substr($date, 3, 2) . substr($date, 0, 2);
  }
}

// if USE_DEFAULT_LANGUAGE_CURRENCY is true, use the following currency, instead of the applications default currency (used when changing language)
define('LANGUAGE_CURRENCY', 'EUR');

// Global entries for the <html> tag
define('HTML_PARAMS','dir="LTR" lang="de"');

define('HEADER_TITLE_TOP', 'Startseite');
define('HEADER_TITLE_CATALOG', 'Katalog');

 // text for gender
define('MALE', 'Herr');
define('FEMALE', 'Frau');
define('MALE_ADDRESS', 'Herr');
define('FEMALE_ADDRESS', 'Frau');

// text for date of birth example
define('DOB_FORMAT_STRING', 'tt.mm.jjjj');
define('BOX_ADD_PRODUCT_ID_TEXT', 'Bitte geben Sie die Artikelnummer aus unserem Katalog ein.');
define('IMAGE_BUTTON_ADD_QUICK', 'Schnellkauf!');
define('BOX_ENTRY_CUSTOMERS','Kunden');
define('BOX_ENTRY_PRODUCTS','Produkte');
define('BOX_ENTRY_REVIEWS','Bewertungen');
define('BOX_TITLE_STATISTICS','Statistik:');

// quick_find box text in includes/boxes/quick_find.php
define('BOX_SEARCH_TEXT', 'Verwenden Sie Stichworte, um ein Produkt zu finden.');
define('BOX_SEARCH_ADVANCED_SEARCH', 'erweiterte Suche');


// reviews box text in includes/boxes/reviews.php
define('BOX_REVIEWS_WRITE_REVIEW', 'Bewerten Sie dieses Produkt!');
define('BOX_REVIEWS_NO_REVIEWS', 'Es liegen noch keine Bewertungen vor');
define('BOX_REVIEWS_TEXT_OF_5_STARS', '%s von 5 Sternen!');

// shopping_cart box text in includes/boxes/shopping_cart.php
define('BOX_SHOPPING_CART_EMPTY', '0 Produkte');

// notifications box text in includes/boxes/products_notifications.php
define('BOX_NOTIFICATIONS_NOTIFY', 'Benachrichtigen Sie mich &uuml;ber Aktuelles zum Artikel <b>%s</b>');
define('BOX_NOTIFICATIONS_NOTIFY_REMOVE', 'Benachrichtigen Sie mich nicht mehr zum Artikel <b>%s</b>');

// manufacturer box text
define('BOX_MANUFACTURER_INFO_HOMEPAGE', '%s Homepage');
define('BOX_MANUFACTURER_INFO_OTHER_PRODUCTS', 'Mehr Produkte');

define('BOX_INFORMATION_CONTACT', 'Kontakt');

// tell a friend box text in includes/boxes/tell_a_friend.php
define('BOX_HEADING_TELL_A_FRIEND', 'Weiterempfehlen');
define('BOX_TELL_A_FRIEND_TEXT', 'Empfehlen Sie diesen Artikel einfach per eMail weiter.');

// pull down default text
define('PULL_DOWN_DEFAULT', 'Bitte wählen');
define('TYPE_BELOW', 'bitte unten eingeben');

// javascript messages
define('JS_ERROR', 'Notwendige Angaben fehlen!\nBitte richtig ausfüllen.\n\n');

define('JS_REVIEW_TEXT', '* Der Text muss mindestens aus ' . REVIEW_TEXT_MIN_LENGTH . ' Buchstaben bestehen.\n');
define('JS_REVIEW_RATING', '* Geben Sie Ihre Bewertung ein.\n');
define('JS_ERROR_NO_PAYMENT_MODULE_SELECTED', '* Bitte wählen Sie eine Zahlungsweise für Ihre Bestellung.\n');
define('JS_ERROR_SUBMITTED', 'Diese Seite wurde bereits bestätigt. Betätigen Sie bitte OK und warten bis der Prozess durchgeführt wurde.');
define('ERROR_NO_PAYMENT_MODULE_SELECTED', 'Bitte wählen Sie eine Zahlungsweise für Ihre Bestellung.');
define('CATEGORY_COMPANY', 'Firmendaten');
define('CATEGORY_PERSONAL', 'Ihre pers&ouml;nlichen Daten');
define('CATEGORY_ADDRESS', 'Ihre Adresse');
define('CATEGORY_CONTACT', 'Ihre Kontaktinformationen');
define('CATEGORY_OPTIONS', 'Optionen');
define('CATEGORY_PASSWORD', 'Ihr Passwort');

define('ENTRY_COMPANY', 'Firmenname:');
define('ENTRY_COMPANY_ERROR', '');
define('ENTRY_COMPANY_TEXT', '');
define('ENTRY_GENDER', 'Anrede:');
define('ENTRY_GENDER_ERROR', 'Bitte wählen Sie die Anrede aus.');
define('ENTRY_GENDER_TEXT', '*');
define('ENTRY_FIRST_NAME', 'Vorname:');
define('ENTRY_FIRST_NAME_ERROR', 'Ihr Vorname muss aus mindestens ' . ENTRY_FIRST_NAME_MIN_LENGTH . ' Zeichen bestehen.');
define('ENTRY_FIRST_NAME_TEXT', '*');
define('ENTRY_LAST_NAME', 'Nachname:');
define('ENTRY_LAST_NAME_ERROR', 'Ihr Nachname muss aus mindestens ' . ENTRY_LAST_NAME_MIN_LENGTH . ' Zeichen bestehen.');
define('ENTRY_LAST_NAME_TEXT', '*');
define('ENTRY_DATE_OF_BIRTH', 'Geburtsdatum:');
define('ENTRY_DATE_OF_BIRTH_ERROR', 'Ihr Geburtsdatum muss im Format TT.MM.JJJJ (zB. 21.05.1970) eingeben werden');
define('ENTRY_DATE_OF_BIRTH_TEXT', '* (zB. 21.05.1970)');
define('ENTRY_EMAIL_ADDRESS', 'eMail-Adresse:');
define('ENTRY_EMAIL_ADDRESS_ERROR', 'Ihre E-Mail Adresse muss aus mindestens ' . ENTRY_EMAIL_ADDRESS_MIN_LENGTH . ' Zeichen bestehen.');
define('ENTRY_EMAIL_ADDRESS_CHECK_ERROR', 'Ihre eingegebene E-Mail Adresse ist fehlerhaft - bitte überprüfen Sie diese.');
define('ENTRY_EMAIL_ADDRESS_ERROR_EXISTS', 'Ihre eingegebene E-Mail Adresse existiert bereits in unserer Datenbank - bitte loggen Sie mit dieser ein, oder erstellen Sie einen neuen Acount mit einer neuen E-Mail Adresse.');
define('ENTRY_EMAIL_ADDRESS_TEXT', '*');
define('ENTRY_STREET_ADDRESS', 'Strasse/Nr.:');
define('ENTRY_STREET_ADDRESS_ERROR', 'Strasse/Nr muss aus mindestens ' . ENTRY_STREET_ADDRESS_MIN_LENGTH . ' Zeichen bestehen.');
define('ENTRY_STREET_ADDRESS_TEXT', '*');
define('ENTRY_SUBURB', 'Stadtteil:');
define('ENTRY_SUBURB_ERROR', '');
define('ENTRY_SUBURB_TEXT', '');
define('ENTRY_POST_CODE', 'Postleitzahl:');
define('ENTRY_POST_CODE_ERROR', 'Ihre Postleitzahl muss aus mindestens ' . ENTRY_POSTCODE_MIN_LENGTH . ' Zeichen bestehen.');
define('ENTRY_POST_CODE_TEXT', '*');
define('ENTRY_CITY', 'Ort:');
define('ENTRY_CITY_ERROR', 'Ort muss aus mindestens ' . ENTRY_CITY_MIN_LENGTH . ' Zeichen bestehen.');
define('ENTRY_CITY_TEXT', '*');
define('ENTRY_STATE', 'Bundesland:');
define('ENTRY_STATE_ERROR', 'Ihr Bundesland muss aus mindestens ' . ENTRY_STATE_MIN_LENGTH . ' Zeichen bestehen.');
define('ENTRY_STATE_ERROR_SELECT', 'Bitte wählen Sie ihr Bundesland aus der Liste aus.');
define('ENTRY_STATE_TEXT', '*');
define('ENTRY_COUNTRY', 'Land:');
define('ENTRY_COUNTRY_ERROR', 'Bitte wählen Sie ihr Land aus der Liste aus.');
define('ENTRY_COUNTRY_TEXT', '*');
define('ENTRY_TELEPHONE_NUMBER', 'Telefonnummer:');
define('ENTRY_TELEPHONE_NUMBER_ERROR', 'Ihre Telefonnummer muss aus mindestens ' . ENTRY_TELEPHONE_MIN_LENGTH . ' Zeichen bestehen.');
define('ENTRY_TELEPHONE_NUMBER_TEXT', '*');
define('ENTRY_FAX_NUMBER', 'Telefaxnummer:');
define('ENTRY_FAX_NUMBER_ERROR', '');
define('ENTRY_FAX_NUMBER_TEXT', '');
define('ENTRY_NEWSLETTER', 'Newsletter:');
define('ENTRY_NEWSLETTER_TEXT', '');
define('ENTRY_NEWSLETTER_YES', 'abonniert');
define('ENTRY_NEWSLETTER_NO', 'nicht abonniert');
define('ENTRY_NEWSLETTER_ERROR', '');
define('ENTRY_PASSWORD', 'Passwort:');
define('ENTRY_PASSWORD_ERROR', 'Ihr Passwort muss aus mindestens ' . ENTRY_PASSWORD_MIN_LENGTH . ' Zeichen bestehen.');
define('ENTRY_PASSWORD_ERROR_NOT_MATCHING', 'Ihre Passwörter stimmen nicht überein.');
define('ENTRY_PASSWORD_TEXT', '*');
define('ENTRY_PASSWORD_CONFIRMATION', 'Best&auml;tigung:');
define('ENTRY_PASSWORD_CONFIRMATION_TEXT', '*');
define('ENTRY_PASSWORD_CURRENT', 'Aktuelles Passwort:');
define('ENTRY_PASSWORD_CURRENT_TEXT', '*');
define('ENTRY_PASSWORD_CURRENT_ERROR', 'Ihr Passwort muss aus mindestens ' . ENTRY_PASSWORD_MIN_LENGTH . ' Zeichen bestehen.');
define('ENTRY_PASSWORD_NEW', 'Neues Passwort:');
define('ENTRY_PASSWORD_NEW_TEXT', '*');
define('ENTRY_PASSWORD_NEW_ERROR', 'Ihr neues Passwort muss aus mindestens ' . ENTRY_PASSWORD_MIN_LENGTH . ' Zeichen bestehen.');
define('ENTRY_PASSWORD_NEW_ERROR_NOT_MATCHING', 'Ihre Passwörter stimmen nicht überein.');
define('PASSWORD_HIDDEN', '--VERSTECKT--');


// constants for use in xtc_prev_next_display function
define('TEXT_RESULT_PAGE', 'Seiten:');
define('TEXT_DISPLAY_NUMBER_OF_PRODUCTS', 'angezeigte Produkte: <b>%d</b> bis <b>%d</b> (von <b>%d</b> insgesamt)');
define('TEXT_DISPLAY_NUMBER_OF_ORDERS', 'angezeigte Bestellungen: <b>%d</b> bis <b>%d</b> (von <b>%d</b> insgesamt)');
define('TEXT_DISPLAY_NUMBER_OF_REVIEWS', 'angezeigte Meinungen: <b>%d</b> bis <b>%d</b> (von <b>%d</b> insgesamt)');
define('TEXT_DISPLAY_NUMBER_OF_PRODUCTS_NEW', 'angezeigte neue Produkte: <b>%d</b> bis <b>%d</b> (von <b>%d</b> insgesamt)');
define('TEXT_DISPLAY_NUMBER_OF_SPECIALS', 'angezeigte Angebote <b>%d</b> bis <b>%d</b> (von <b>%d</b> insgesamt)');

define('PREVNEXT_TITLE_FIRST_PAGE', 'erste Seite');
define('PREVNEXT_TITLE_PREVIOUS_PAGE', 'vorherige Seite');
define('PREVNEXT_TITLE_NEXT_PAGE', 'n&auml;chste Seite');
define('PREVNEXT_TITLE_LAST_PAGE', 'letzte Seite');
define('PREVNEXT_TITLE_PAGE_NO', 'Seite %d');
define('PREVNEXT_TITLE_PREV_SET_OF_NO_PAGE', 'Vorhergehende %d Seiten');
define('PREVNEXT_TITLE_NEXT_SET_OF_NO_PAGE', 'N&auml;chste %d Seiten');
define('PREVNEXT_BUTTON_FIRST', '&lt;&lt;ERSTE');
define('PREVNEXT_BUTTON_PREV', '[&lt;&lt;&#160;vorherige]');
define('PREVNEXT_BUTTON_NEXT', '[n&auml;chste&#160;&gt;&gt;]');
define('PREVNEXT_BUTTON_LAST', 'LETZTE&gt;&gt;');

define('IMAGE_BUTTON_ADD_ADDRESS', 'Neue Adresse');
define('IMAGE_BUTTON_ADDRESS_BOOK', 'Adressbuch');
define('IMAGE_BUTTON_BACK', 'Zurück');
define('IMAGE_BUTTON_CHANGE_ADDRESS', 'Adresse ändern');
define('IMAGE_BUTTON_CHECKOUT', 'Kasse');
define('IMAGE_BUTTON_CONFIRM_ORDER', 'Bestellung bestätigen');
define('IMAGE_BUTTON_CONTINUE', 'Weiter');
define('IMAGE_BUTTON_CONTINUE_SHOPPING', 'Einkauf fortsetzen');
define('IMAGE_BUTTON_DELETE', 'Löschen');
define('IMAGE_BUTTON_EDIT_ACCOUNT', 'Daten ändern');
define('IMAGE_BUTTON_HISTORY', 'Bestellübersicht');
define('IMAGE_BUTTON_LOGIN', 'Anmelden');
define('IMAGE_BUTTON_IN_CART', 'In den Warenkorb');
define('IMAGE_BUTTON_NOTIFICATIONS', 'Benachrichtigungen');
define('IMAGE_BUTTON_QUICK_FIND', 'Schnellsuche');
define('IMAGE_BUTTON_REMOVE_NOTIFICATIONS', 'Benachrichtigungen löschen');
define('IMAGE_BUTTON_REVIEWS', 'Bewertungen');
define('IMAGE_BUTTON_SEARCH', 'Suchen');
define('IMAGE_BUTTON_SHIPPING_OPTIONS', 'Versandoptionen');
define('IMAGE_BUTTON_TELL_A_FRIEND', 'Weiterempfehlen');
define('IMAGE_BUTTON_UPDATE', 'Aktualisieren');
define('IMAGE_BUTTON_UPDATE_CART', 'Warenkorb aktualisieren');
define('IMAGE_BUTTON_WRITE_REVIEW', 'Bewertung schreiben');

define('SMALL_IMAGE_BUTTON_DELETE', 'Delete');
define('SMALL_IMAGE_BUTTON_EDIT', 'Edit');
define('SMALL_IMAGE_BUTTON_VIEW', 'View');

define('ICON_ARROW_RIGHT', 'Zeige mehr');
define('ICON_CART', 'In den Warenkorb');
define('ICON_SUCCESS', 'Success');
define('ICON_WARNING', 'Warnung');

define('TEXT_GREETING_PERSONAL', 'Sch&ouml;n das Sie wieder da sind <span class="greetUser">%s!</span> M&ouml;chten Sie die <a href="%s"><u>neue Produkte</u></a> ansehen?');
define('TEXT_GREETING_PERSONAL_RELOGON', '<small>Wenn Sie nicht %s sind, melden Sie sich bitte <a href="%s"><u>hier</u></a> mit Ihrem Kundenkonto an.</small>');
define('TEXT_GREETING_GUEST', 'Herzlich Willkommen <span class="greetUser">Gast!</span> M&ouml;chten Sie sich <a href="%s"><u>anmelden</u></a>? Oder wollen Sie ein <a href="%s"><u>Kundenkonto</u></a> er&ouml;ffnen?');

define('TEXT_SORT_PRODUCTS', 'Sortierung der Artikel ist ');
define('TEXT_DESCENDINGLY', 'absteigend');
define('TEXT_ASCENDINGLY', 'aufsteigend');
define('TEXT_BY', ' nach ');

define('TEXT_REVIEW_BY', 'von %s');
define('TEXT_REVIEW_WORD_COUNT', '%s Worte');
define('TEXT_REVIEW_RATING', 'Bewertung: %s [%s]');
define('TEXT_REVIEW_DATE_ADDED', 'Datum hinzugef&uuml;gt: %s');
define('TEXT_NO_REVIEWS', 'Es liegen noch keine Bewertungen vor.');

define('TEXT_NO_NEW_PRODUCTS', 'Zur Zeit gibt es keine neuen Produkte.');

define('TEXT_UNKNOWN_TAX_RATE', 'Unbekannter Steuersatz');

define('TEXT_REQUIRED', '<span class="errorText">erforderlich</span>');

define('ERROR_TEP_MAIL', '<font face="Verdana, Arial" size="2" color="#ff0000"><b><small>Fehler:</small> Die eMail kann nicht &uuml;ber den angegebenen SMTP-Server verschickt werden. Bitte kontrollieren Sie die Einstellungen in der php.ini Datei und f&uuml;hren Sie notwendige Korrekturen durch!</b></font>');
define('WARNING_INSTALL_DIRECTORY_EXISTS', 'Warnung: Das Installationverzeichnis ist noch vorhanden auf: ' . dirname($HTTP_SERVER_VARS['SCRIPT_FILENAME']) . '/xtc_installer. Bitte l&ouml;schen Sie das Verzeichnis aus Gr&uuml;nden der Sicherheit!');
define('WARNING_CONFIG_FILE_WRITEABLE', 'Warnung: XT-Commerce kann in die Konfigurationsdatei schreiben: ' . dirname($HTTP_SERVER_VARS['SCRIPT_FILENAME']) . '/includes/configure.php. Das stellt ein m&ouml;gliches Sicherheitsrisiko dar - bitte korrigieren Sie die Benutzerberechtigungen zu dieser Datei!');
define('WARNING_SESSION_DIRECTORY_NON_EXISTENT', 'Warnung: Das Verzeichnis f&uuml;r die Sessions existiert nicht: ' . xtc_session_save_path() . '. Die Sessions werden nicht funktionieren bis das Verzeichnis erstellt wurde!');
define('WARNING_SESSION_DIRECTORY_NOT_WRITEABLE', 'Warnung: XT-Commerce kann nicht in das Sessions Verzeichnis schreiben: ' . xtc_session_save_path() . '. Die Sessions werden nicht funktionieren bis die richtigen Benutzerberechtigungen gesetzt wurden!');
define('WARNING_SESSION_AUTO_START', 'Warnung: session.auto_start ist aktiviert (enabled) - Bitte deaktivieren (disabled) Sie dieses PHP Feature in der php.ini und starten Sie den WEB-Server neu!');
define('WARNING_DOWNLOAD_DIRECTORY_NON_EXISTENT', 'Warnung: Das Verzeichnis für den Artikel Download existiert nicht: ' . DIR_FS_DOWNLOAD . '. Diese Funktion wird nicht funktionieren bis das Verzeichnis erstellt wurde!');

define('TEXT_CCVAL_ERROR_INVALID_DATE', 'Das "G&uuml;ltig bis" Datum ist ung&uuml;ltig.<br>Bitte korrigieren Sie Ihre Angaben.');
define('TEXT_CCVAL_ERROR_INVALID_NUMBER', 'Die "KreditkarteNummer", die Sie angegeben haben, ist ung&uuml;ltig.<br>Bitte korrigieren Sie Ihre Angaben.');
define('TEXT_CCVAL_ERROR_UNKNOWN_CARD', 'Die ersten 4 Ziffern Ihrer Kreditkarte sind: %s<br>Wenn diese Angaben stimmen, wird dieser Kartentyp leider nicht akzeptiert.<br>Bitte korrigieren Sie Ihre Angaben gegebenfalls.');

/*
  The following copyright announcement can only be
  appropriately modified or removed if the layout of
  the site theme has been modified to distinguish
  itself from the default osCommerce-copyrighted
  theme.

  Please leave this comment intact together with the
  following copyright announcement.

  Copyright announcement changed due to the permissions
  from LG Hamburg from 28th February 2003 / AZ 308 O 70/03
*/
define('FOOTER_TEXT_BODY', 'Copyright &copy; 2003 <a href="http://www.xt-commerce.com" target="_blank">XT-Commerce</a><br>Powered by <a href="http://www.xt-commerce.com" target="_blank">XT-Commerce</a>');

//  conditions check

define('ERROR_CONDITIONS_NOT_ACCEPTED', 'Sofern Sie unsere AGB\'s nicht akzeptieren, können wir Ihre Bestellung bedauerlicherweise nicht entgegen nehmen!');

define('SUB_TITLE_OT_DISCOUNT','Rabatt:');
define('SUB_TITLE_SUB_NEW','Summe:');

define('NOT_ALLOWED_TO_SEE_PRICES','Sie haben keine Erlaubnis Preise zu sehen');
define('NOT_ALLOWED_TO_ADD_TO_CART','Sie haben keine Erlaubnis Produkte in den Warenkorb zu legen');

define('BOX_LOGINBOX_HEADING', 'Willkommen zurück!');
define('BOX_LOGINBOX_EMAIL', 'E-Mail Adresse:');
define('BOX_LOGINBOX_PASSWORD', 'Passwort:');
define('IMAGE_BUTTON_LOGIN', 'Login');
define('BOX_ACCOUNTINFORMATION_HEADING','Information');

define('BOX_LOGINBOX_STATUS','Kundengruppe:');
define('BOX_LOGINBOX_INCL','Alle Preise incl. UST');
define('BOX_LOGINBOX_EXCL','Alle Preise excl. UST');
define('TAX_ADD_TAX','inkl. ');
define('TAX_NO_TAX','zzgl. ');
define('BOX_LOGINBOX_DISCOUNT','Produktrabatt');
define('BOX_LOGINBOX_DISCOUNT_TEXT','Rabatt');
define('BOX_LOGINBOX_DISCOUNT_OT','');

define('NOT_ALLOWED_TO_SEE_PRICES_TEXT','Sie haben keine Erlaubnis Preise zu sehen, erstellen Sie bitte einen Account.');

define('TEXT_DOWNLOAD','Runterladen');
define('TEXT_VIEW','Ansehen');

define('TEXT_BUY', '1 x \'');
define('TEXT_NOW', '\' bestellen');
define('TEXT_GUEST','Gast');
define('TEXT_NO_PURCHASES', 'Sie haben noch keine Bestellungen get&auml;tigt.');


// Warnings
define('SUCCESS_ACCOUNT_UPDATED', 'Ihr Konto wurde erfolgreich upgedated.');
define('SUCCESS_NEWSLETTER_UPDATED', 'Ihre Newsletter Abonnements wurden erfolgreich aktualisiert!');
define('SUCCESS_NOTIFICATIONS_UPDATED', 'Ihre Produktbenachrichtigungen wurden erfolgreich aktualisiert!');
define('SUCCESS_PASSWORD_UPDATED', 'Ihr Passwort wurde erfolgreich ge&auml;ndert!');
define('ERROR_CURRENT_PASSWORD_NOT_MATCHING', 'Das eingegebene Passwort stimmt nicht mit dem gespeichertem Passwort &uuml;berein. Bitte probieren Sie es noch einmal.');
define('TEXT_MAXIMUM_ENTRIES', '<font color="#ff0000"><b>Hinweis:</b></font> Ihnen stehen %s Adressbucheintr&auml;ge zur Verf&uuml;gung!');
define('SUCCESS_ADDRESS_BOOK_ENTRY_DELETED', 'Der ausgew&auml;hlte Eintrag wurde erflogreich gel&ouml;scht.');
define('SUCCESS_ADDRESS_BOOK_ENTRY_UPDATED', 'Ihr Adressbuch wurde erfolgreich aktualisiert!');
define('WARNING_PRIMARY_ADDRESS_DELETION', 'Die Standardadresse kann nicht gel&ouml;scht werden. Bitte erst eine andere Standardadresse w&auml;hlen. Danach kann der Eintrag gel&ouml;scht werden.');
define('ERROR_NONEXISTING_ADDRESS_BOOK_ENTRY', 'Dieser Adressbucheintrag ist nicht vorhanden.');
define('ERROR_ADDRESS_BOOK_FULL', 'Ihr Adressbuch kann keine weiteren Adressen aufnehmen. Bitte l&ouml;schen Sie eine nicht mehr ben&ouml;tigte Adresse. Danach k&ouml;nnen Sie einen neuen Eintrag speichern.');

//Advanced Search
define('ENTRY_CATEGORIES', 'Kategorien:');
define('ENTRY_INCLUDE_SUBCATEGORIES', 'Unterkategorien mit einbeziehen');
define('ENTRY_MANUFACTURERS', 'Hersteller:');
define('ENTRY_PRICE_FROM', 'Preis ab:');
define('ENTRY_PRICE_TO', 'Preis bis:');
define('TEXT_ALL_CATEGORIES', 'Alle Kategorien');
define('TEXT_ALL_MANUFACTURERS', 'Alle Hersteller');
define('JS_AT_LEAST_ONE_INPUT', '* Eines der folgenden Felder muss ausgefüllt werden:\n    Stichworte\n    Datum hinzugefügt von\n    Datum hinzugefügt bis\n    Preis ab\n    Preis bis\n');
define('JS_INVALID_FROM_DATE', '* Unzulässiges von Datum\n');
define('JS_INVALID_TO_DATE', '* Unzulässiges bis jetzt\n');
define('JS_TO_DATE_LESS_THAN_FROM_DATE', '* Das Datum von muss grösser oder gleich bis jetzt sein\n');
define('JS_PRICE_FROM_MUST_BE_NUM', '* Preis ab, muss eine Zahl sein\n');
define('JS_PRICE_TO_MUST_BE_NUM', '* Preis bis, muss eine Zahl sein\n');
define('JS_PRICE_TO_LESS_THAN_PRICE_FROM', '* Preis bis muss größer oder gleich Preis ab sein.\n');
define('JS_INVALID_KEYWORDS', '* Suchbegriff unzulässig\n');
define('TEXT_NO_PRODUCTS', 'Es wurden keine Artikel gefunden, die den Suchkriterien entsprechen.');
define('TEXT_ORIGIN_LOGIN', '<font color="#FF0000"><small><b>ACHTUNG:</b></font></small> Wenn Sie bereits ein Konto besitzen, so melden Sie sich bitte <a href="%s"><u><b>hier</b></u></a> an.');
define('TEXT_LOGIN_ERROR', '<font color="#ff0000"><b>FEHLER:</b></font> Keine &Uuml;bereinstimmung der eingebenen \'eMail-Adresse\' und/oder dem \'Passwort\'.');
define('TEXT_VISITORS_CART', '<font color="#ff0000"><b>ACHTUNG:</b></font> Ihre Besuchereingaben werden automatisch mit Ihrem Kundenkonto verbunden. <a href="javascript:session_win();">[Mehr Information]</a>');
define('TEXT_NO_EMAIL_ADDRESS_FOUND', '<font color="#ff0000"><b>ACHTUNG:</b></font> Die eingegebene eMail-Adresse ist nicht registriert. Bitte versuchen Sie es noch einmal.');
define('TEXT_PASSWORD_SENT', 'Ein neues Passwort wurde per eMail verschickt.');
define('TEXT_PRODUCT_NOT_FOUND', 'Produkt wurde nicht gefunden!');
define('TEXT_MORE_INFORMATION', 'F&uuml;r weitere Informationen, besuchen Sie bitte die <a href="%s" target="_blank"><u>Homepage</u></a> zu diesem Produkt.');
define('TEXT_DATE_ADDED', 'Dieses Produkt haben wir am %s in unseren Katalog aufgenommen.');
define('TEXT_DATE_AVAILABLE', '<font color="#ff0000">Dieses Produkt wird voraussichtlich ab dem %s wieder vorr&auml;tig sein.</font>');
define('TEXT_CART_EMPTY', 'Sie haben noch nichts in Ihrem Warenkorb.');
define('SUB_TITLE_SUB_TOTAL', 'Zwischensumme:');
define('SUB_TITLE_TOTAL', 'Summe:');

define('OUT_OF_STOCK_CANT_CHECKOUT', 'Die mit ' . STOCK_MARK_PRODUCT_OUT_OF_STOCK . ' markierten Produkte, sind leider nicht in der von Ihnen gew&uuml;nschten Menge auf Lager.<br>Bitte reduzieren Sie Ihre Bestellmenge f&uuml;r die gekennzeichneten Produkte, vielen Dank');
define('OUT_OF_STOCK_CAN_CHECKOUT', 'Die mit ' . STOCK_MARK_PRODUCT_OUT_OF_STOCK . ' markierten Produkte, sind leider nicht in der von Ihnen gew&uuml;nschten Menge auf Lager.<br>Die bestellte Menge wird kurzfristig von uns geliefert, wenn Sie es w&uuml;nschen nehmen wir auch eine Teillieferung vor.');

define('HEADING_TITLE_TELL_A_FRIEND', 'Empfehlen Sie \'%s\' weiter');
define('HEADING_TITLE_ERROR_TELL_A_FRIEND', 'Produkt weiterempfehlen');
define('ERROR_INVALID_PRODUCT', 'Das von Ihnen gew&auml;hlte Produkt wurde nicht gefunden!');


define('NAVBAR_TITLE_ACCOUNT', 'Ihr Konto');
define('NAVBAR_TITLE_1_ACCOUNT_EDIT', 'Ihr Konto');
define('NAVBAR_TITLE_2_ACCOUNT_EDIT', 'Ihre pers&ouml;nliche Daten &auml;ndern');
define('NAVBAR_TITLE_1_ACCOUNT_HISTORY', 'Ihr Konto');
define('NAVBAR_TITLE_2_ACCOUNT_HISTORY', 'Ihre get&auml;tigten Bestellungen');
define('NAVBAR_TITLE_1_ACCOUNT_HISTORY_INFO', 'Ihr Konto');
define('NAVBAR_TITLE_2_ACCOUNT_HISTORY_INFO', 'Get&auml;tigte Bestellung');
define('NAVBAR_TITLE_3_ACCOUNT_HISTORY_INFO', 'Bestellnummer %s');
define('NAVBAR_TITLE_1_ACCOUNT_NEWSLETTERS', 'Ihr Konto');
define('NAVBAR_TITLE_2_ACCOUNT_NEWSLETTERS', 'Newsletter Abonnements');
define('NAVBAR_TITLE_1_ACCOUNT_NOTIFICATIONS', 'Ihr Konto');
define('NAVBAR_TITLE_2_ACCOUNT_NOTIFICATIONS', 'Produktbenachrichtungen');
define('NAVBAR_TITLE_1_ACCOUNT_PASSWORD', 'Ihr Konto');
define('NAVBAR_TITLE_2_ACCOUNT_PASSWORD', 'Passwort &auml;ndern');
define('NAVBAR_TITLE_1_ADDRESS_BOOK', 'Ihr Konto');
define('NAVBAR_TITLE_2_ADDRESS_BOOK', 'Adressbuch');
define('NAVBAR_TITLE_1_ADDRESS_BOOK_PROCESS', 'Ihr Konto');
define('NAVBAR_TITLE_2_ADDRESS_BOOK_PROCESS', 'Adressbuch');
define('NAVBAR_TITLE_ADD_ENTRY_ADDRESS_BOOK_PROCESS', 'Neuer Eintrag');
define('NAVBAR_TITLE_MODIFY_ENTRY_ADDRESS_BOOK_PROCESS', 'Eintrag &auml;ndern');
define('NAVBAR_TITLE_DELETE_ENTRY_ADDRESS_BOOK_PROCESS', 'Delete Entry');
define('NAVBAR_TITLE_ADVANCED_SEARCH', 'Erweiterte Suche');
define('NAVBAR_TITLE1_ADVANCED_SEARCH', 'Erweiterte Suche');
define('NAVBAR_TITLE2_ADVANCED_SEARCH', 'Suchergebnisse');
define('NAVBAR_TITLE_1_CHECKOUT_CONFIRMATION', 'Kasse');
define('NAVBAR_TITLE_2_CHECKOUT_CONFIRMATION', 'Best&auml;tigung');
define('NAVBAR_TITLE_1_CHECKOUT_PAYMENT', 'Kasse');
define('NAVBAR_TITLE_2_CHECKOUT_PAYMENT', 'Zahlungsweise');
define('NAVBAR_TITLE_1_PAYMENT_ADDRESS', 'Kasse');
define('NAVBAR_TITLE_2_PAYMENT_ADDRESS', 'Rechnungsadresse &auml;ndern');
define('NAVBAR_TITLE_1_CHECKOUT_SHIPPING', 'Kasse');
define('NAVBAR_TITLE_2_CHECKOUT_SHIPPING', 'Versandinformationen');
define('NAVBAR_TITLE_1_CHECKOUT_SHIPPING_ADDRESS', 'Kasse');
define('NAVBAR_TITLE_2_CHECKOUT_SHIPPING_ADDRESS', 'Versandadresse &auml;ndern');
define('NAVBAR_TITLE_1_CHECKOUT_SUCCESS', 'Kasse');
define('NAVBAR_TITLE_2_CHECKOUT_SUCCESS', 'Erfolg');
define('NAVBAR_TITLE_CONTACT_US', 'Kontakt');
define('NAVBAR_TITLE_CREATE_ACCOUNT', 'Konto erstellen');
define('NAVBAR_TITLE_1_CREATE_ACCOUNT_SUCCESS', 'Konto erstellen');
define('NAVBAR_TITLE_2_CREATE_ACCOUNT_SUCCESS', 'Erfolg');
if ($navigation->snapshot['page'] == FILENAME_CHECKOUT_SHIPPING) {
  define('NAVBAR_TITLE_LOGIN', 'Bestellen');
} else {
  define('NAVBAR_TITLE_LOGIN', 'Anmelden');
}
define('NAVBAR_TITLE_LOGOFF','Auf Wiedersehen');
define('NAVBAR_TITLE_1_PASSWORD_FORGOTTEN', 'Anmelden');
define('NAVBAR_TITLE_2_PASSWORD_FORGOTTEN', 'Passwort vergessen');
define('NAVBAR_TITLE_PRODUCTS_NEW', 'Neue Produkte');
define('NAVBAR_TITLE_SHOPPING_CART', 'Warenkorb');
define('NAVBAR_TITLE_SPECIALS', 'Angebote');
define('NAVBAR_TITLE_COOKIE_USAGE', 'Cookie Usage');
define('NAVBAR_TITLE_PRODUCT_REVIEWS', 'Meinungen');
define('NAVBAR_TITLE_TELL_A_FRIEND', 'Produkt weiterempfehlen');
define('NAVBAR_TITLE_REVIEWS_WRITE', 'Meinungen');
define('NAVBAR_TITLE_REVIEWS','Rezensionen');
define('NAVBAR_TITLE_SSL_CHECK', 'Sicherheitshinweis');
define('NAVBAR_TITLE_CREATE_GUEST_ACCOUNT','Konto erstellen');




?>
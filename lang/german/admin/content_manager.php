<?php
/* --------------------------------------------------------------
   $Id: content_manager.php,v 1.1 2003/09/28 14:38:01 fanta2k Exp $   

   XT-Commerce - community made shopping
   http://www.xt-commerce.com

   Copyright (c) 2003 XT-Commerce
   --------------------------------------------------------------
   based on:
   (c) 2003	 nextcommerce (content_manager.php,v 1.8 2003/08/25); www.nextcommerce.org
   
   Released under the GNU General Public License 
   --------------------------------------------------------------*/
   
 define('HEADING_TITLE','Content Manager');
 define('HEADING_CONTENT','Site content');
 define('HEADING_PRODUCTS_CONTENT','Products content');
 define('TABLE_HEADING_CONTENT_ID','LinkID');
 define('TABLE_HEADING_CONTENT_TITLE','Titel');
 define('TABLE_HEADING_CONTENT_FILE','Datei');
 define('TABLE_HEADING_CONTENT_STATUS','In Box sichtbar');
 define('TABLE_HEADING_CONTENT_BOX','Box');
 define('TABLE_HEADING_PRODUCTS_ID','ID');
 define('TABLE_HEADING_PRODUCTS','Produkt');
 define('TABLE_HEADING_PRODUCTS_CONTENT_ID','ID');
 define('TABLE_HEADING_LANGUAGE','Sprache');
 define('TABLE_HEADING_CONTENT_NAME','Name/Dateiname');
 define('TABLE_HEADING_CONTENT_LINK','Link');
 define('TABLE_HEADING_CONTENT_HITS','Hits');
 define('TABLE_HEADING_CONTENT_GROUP','Gruppe');
 define('TEXT_YES','Ja');
 define('TEXT_NO','Nein');
 define('TABLE_HEADING_CONTENT_ACTION','Aktion');
 define('TEXT_DELETE','Löschen');
 define('TEXT_EDIT','Bearbeiten');
 define('TEXT_PREVIEW','Vorschau');
 define('CONFIRM_DELETE','Wollen Sie den Content wirklich löschen ?');
 define('CONTENT_NOTE','Content markiert mit <font color="ff0000">*</font> gehört zum System und kann nicht gelöscht werden!');

 
 // edit
 define('TEXT_LANGUAGE','Sprache:');
 define('TEXT_STATUS','Sichtbar:');
 define('TEXT_STATUS_DESCRIPTION','Wenn ausgewählt, wird ein Link in der Box angezeigt');
 define('TEXT_TITLE','Titel:');
 define('TEXT_TITLE_FILE','Titel/Dateiname:');
 define('TEXT_SELECT','-Auswahl-');
 define('TEXT_HEADING','Überschrift:');
 define('TEXT_CONTENT','Text:');
 define('TEXT_UPLOAD_FILE','Datei Laden:');
 define('TEXT_UPLOAD_FILE_LOCAL','(von Ihrem lokalen System)');
 define('TEXT_CHOOSE_FILE','Datei Wählen:');
 define('TEXT_CHOOSE_FILE_DESC','Sie können ebenfals eine Bereits verwendete Datei aus der Liste auswählen.');
 define('TEXT_NO_FILE','Auswahl löschen');
 define('TEXT_CHOOSE_FILE_SERVER','(Falls Sie ihre Datein selbst via FTP auf ihren Server gespeichert haben <i>(media/content)</i>, können Sie hier das File auswählen.');
 define('TEXT_CURRENT_FILE','Verwendete Datei:');
 define('TEXT_FILE_DESCRIPTION','<b>Info:</b><br>Sie haben ebenfalls die Möglichkeit eine <b>.htlm</b> oder <b>.htm</b> Datei einzubinden.<br> Falls Sie eine Datei auswählen wird der Text im Textfeld ignoriert.<br><br>'); 
 define('ERROR_FILE','Falsches Dateiformat (nur .html od .htm)');
 define('ERROR_TITLE','Bitte geben Sie einen Titel ein');
 define('ERROR_COMMENT','Bitte geben Sie eine Dateibeschreibung ein!');
 define('TEXT_FILE_FLAG','Box:');
 define('TEXT_PARENT','Hauptdokument:');
 define('TEXT_PARENT_DESCRIPTION','Diesem Dokument zuweisen');
 define('TEXT_PRODUCT','Produkt:');
 define('TEXT_LINK','Link:');
 define('TEXT_GROUP','Sprachgruppe:');
 define('TEXT_GROUP_DESC','Mit dieser ID verknüpfen sie gleiche Themen unterschiedlicher Sprachen miteinander.');
 
 define('TEXT_CONTENT_DESCRIPTION','Mit diesem Content Manager haben Sie die Möglichkeit, jede beliebige Datei einem Produkt hinzuzufügen.<br>Zb. Produktbeschreibungen, Handbücher, technische Datenblätter,Hörproben, usw...<br>Diese Elemente werden In der Produkt Detailansicht angezeigt.<br><br>');
 define('TEXT_FILENAME','Benutze Datei:');
 define('TEXT_FILE_DESC','Beschreibung:');
 define('USED_SPACE','Benützer Speicher:');
 define('TABLE_HEADING_CONTENT_FILESIZE','Dateigröße');
   
 
 ?>
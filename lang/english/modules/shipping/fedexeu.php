<?php
/* -----------------------------------------------------------------------------------------
   $Id: fedexeu.php,v 1.1 2003/12/19 13:19:08 fanta2k Exp $   

   XT-Commerce - community made shopping
   http://www.xt-commerce.com

   Copyright (c) 2003 XT-Commerce
   -----------------------------------------------------------------------------------------
   based on: 
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce( fedexeu.php,v 1.01 2003/02/18 03:25:00); www.oscommerce.com 
   (c) 2003	 nextcommerce (fedexeu.php,v 1.5 2003/08/1); www.nextcommerce.org

   Released under the GNU General Public License 
   -----------------------------------------------------------------------------------------
   Third Party contributions:
   fedex_europe_1.02        	Autor:	Copyright (C) 2002 - 2003 TheMedia, Dipl.-Ing Thomas Plänkers | http://www.themedia.at & http://www.oscommerce.at

   Released under the GNU General Public License 
   ---------------------------------------------------------------------------------------*/



define('MODULE_SHIPPING_FEDEXEU_TEXT_TITLE', 'FedEx Express Europe');
define('MODULE_SHIPPING_FEDEXEU_TEXT_DESCRIPTION', 'FedEx Express Europe');
define('MODULE_SHIPPING_FEDEXEU_TEXT_WAY', 'Dispatch to');
define('MODULE_SHIPPING_FEDEXEU_TEXT_UNITS', 'kg');
define('MODULE_SHIPPING_FEDEXEU_INVALID_ZONE', 'Unfortunately it is not possible to dispatch into this country');
define('MODULE_SHIPPING_FEDEXEU_UNDEFINED_RATE', 'Forwarding expenses cannot be calculated for the moment');

define('MODULE_SHIPPING_FEDEXEU_STATUS_TITLE' , 'FedEx Express Europe');
define('MODULE_SHIPPING_FEDEXEU_STATUS_DESC' , 'Wollen Sie den Versand durch FedEx Express Europe anbieten?');
define('MODULE_SHIPPING_FEDEXEU_HANDLING_TITLE' , 'Handling Fee');
define('MODULE_SHIPPING_FEDEXEU_HANDLING_DESC' , 'Bearbeitungsgebühr für diese Versandart in Euro');
define('MODULE_SHIPPING_FEDEXEU_TAX_CLASS_TITLE' , 'Steuersatz');
define('MODULE_SHIPPING_FEDEXEU_TAX_CLASS_DESC' , 'Wählen Sie den MwSt.-Satz für diese Versandart aus.');
define('MODULE_SHIPPING_FEDEXEU_ZONE_TITLE' , 'Versand Zone');
define('MODULE_SHIPPING_FEDEXEU_ZONE_DESC' , 'Wenn Sie eine Zone auswählen, wird diese Versandart nur in dieser Zone angeboten.');
define('MODULE_SHIPPING_FEDEXEU_SORT_ORDER_TITLE' , 'Reihenfolge der Anzeige');
define('MODULE_SHIPPING_FEDEXEU_SORT_ORDER_DESC' , 'Niedrigste wird zuerst angezeigt.');
define('MODULE_SHIPPING_FEDEXEU_ALLOWED_TITLE' , 'Einzelne Versandzonen');
define('MODULE_SHIPPING_FEDEXEU_ALLOWED_DESC' , 'Geben Sie <b>einzeln</b> die Zonen an, in welche ein Versand möglich sein soll. zb AT,DE');
define('MODULE_SHIPPING_FEDEXEU_COUNTRIES_1_TITLE' , 'Europazone 1 Länder');
define('MODULE_SHIPPING_FEDEXEU_COUNTRIES_1_DESC' , 'Eurozone');
define('MODULE_SHIPPING_FEDEXEU_COST_PAK_1_TITLE' , 'Tariftabelle für Zone 1 bis 2.50 kg PAK');
define('MODULE_SHIPPING_FEDEXEU_COST_PAK_1_DESC' , 'Tarif Tabelle für die Zone 1, basiered auf <b>\'PAK\'</b> bis 2.50 kg Versandgewicht.');
define('MODULE_SHIPPING_FEDEXEU_COST_BOX_1_TITLE' , 'Tariftabelle für Zone 1 bis 10 kg BOX');
define('MODULE_SHIPPING_FEDEXEU_COST_BOX_1_DESC' , 'Tarif Tabelle für die Zone 1, basiered auf <b>\'BOX\'</b> bis 10 kg Versandgewicht.');
define('MODULE_SHIPPING_FEDEXEU_STEP_BOX_20_1_TITLE' , 'Erhöhungszuschlag bis 20 kg BOX');
define('MODULE_SHIPPING_FEDEXEU_STEP_BOX_20_1_DESC' , 'Erhöhungszuschlag pro übersteigende 0,50 kg in EUR');
define('MODULE_SHIPPING_FEDEXEU_STEP_BOX_40_1_TITLE' , 'Erhöhungszuschlag bis 40 kg BOX');
define('MODULE_SHIPPING_FEDEXEU_STEP_BOX_40_1_DESC' , 'Erhöhungszuschlag pro übersteigende 0,50 kg in EUR');
define('MODULE_SHIPPING_FEDEXEU_STEP_BOX_70_1_TITLE' , 'Erhöhungszuschlag bis 70 kg BOX');
define('MODULE_SHIPPING_FEDEXEU_STEP_BOX_70_1_DESC' , 'Erhöhungszuschlag pro übersteigende 0,50 kg in EUR');
define('MODULE_SHIPPING_FEDEXEU_COUNTRIES_2_TITLE' , 'Europazone 2 Länder');
define('MODULE_SHIPPING_FEDEXEU_COUNTRIES_2_DESC' , 'Durch Komma getrennt Liste der Länder als zwei Zeichen ISO-Code Landeskennzahlen, die Teil der Zone 1 sind.');
define('MODULE_SHIPPING_FEDEXEU_COST_PAK_2_TITLE' , 'Tariftabelle für Zone 2 bis 2.50 kg PAK');
define('MODULE_SHIPPING_FEDEXEU_COST_PAK_2_DESC' , 'Tarif Tabelle für die Zone 2, basiered auf <b>\'PAK\'</b> bis 2.50 kg Versandgewicht.');
define('MODULE_SHIPPING_FEDEXEU_COST_BOX_2_TITLE' , 'Tariftabelle für Zone 2 bis 10 kg BOX');
define('MODULE_SHIPPING_FEDEXEU_COST_BOX_2_DESC' , 'Tarif Tabelle für die Zone 2, basiered auf <b>\'BOX\'</b> bis 10 kg Versandgewicht.');
define('MODULE_SHIPPING_FEDEXEU_STEP_BOX_20_2_TITLE' , 'Erhöhungszuschlag bis 20 kg BOX');
define('MODULE_SHIPPING_FEDEXEU_STEP_BOX_20_2_DESC' , 'Erhöhungszuschlag pro übersteigende 0,50 kg in EUR');
define('MODULE_SHIPPING_FEDEXEU_STEP_BOX_40_2_TITLE' , 'Erhöhungszuschlag bis 40 kg BOX');
define('MODULE_SHIPPING_FEDEXEU_STEP_BOX_40_2_DESC' , 'Erhöhungszuschlag pro übersteigende 0,50 kg in EUR');
define('MODULE_SHIPPING_FEDEXEU_STEP_BOX_70_2_TITLE' , 'Erhöhungszuschlag bis 70 kg BOX');
define('MODULE_SHIPPING_FEDEXEU_STEP_BOX_70_2_DESC' , 'Erhöhungszuschlag pro übersteigende 0,50 kg in EUR');
define('MODULE_SHIPPING_FEDEXEU_COUNTRIES_3_TITLE' , 'Europazone 3 Länder');
define('MODULE_SHIPPING_FEDEXEU_COUNTRIES_3_DESC' , 'Durch Komma getrennt Liste der Länder als zwei Zeichen ISO-Code Landeskennzahlen, die Teil der Zone 2 sind.');
define('MODULE_SHIPPING_FEDEXEU_COST_PAK_3_TITLE' , 'Tariftabelle für Zone 3 bis 2.50 kg PAK');
define('MODULE_SHIPPING_FEDEXEU_COST_PAK_3_DESC' , 'Tarif Tabelle für die Zone 3, basiered auf <b>\'PAK\'</b> bis 2.50 kg Versandgewicht.');
define('MODULE_SHIPPING_FEDEXEU_COST_BOX_3_TITLE' , 'Tariftabelle für Zone 3 bis 10 kg BOX');
define('MODULE_SHIPPING_FEDEXEU_COST_BOX_3_DESC' , 'Tarif Tabelle für die Zone 3, basiered auf <b>\'BOX\'</b> bis 10 kg Versandgewicht.');
define('MODULE_SHIPPING_FEDEXEU_STEP_BOX_20_3_TITLE' , 'Erhöhungszuschlag bis 20 kg BOX');
define('MODULE_SHIPPING_FEDEXEU_STEP_BOX_20_3_DESC' , 'Erhöhungszuschlag pro übersteigende 0,50 kg in EUR');
define('MODULE_SHIPPING_FEDEXEU_STEP_BOX_40_3_TITLE' , 'Erhöhungszuschlag bis 40 kg BOX');
define('MODULE_SHIPPING_FEDEXEU_STEP_BOX_40_3_DESC' , 'Erhöhungszuschlag pro übersteigende 0,50 kg in EUR');
define('MODULE_SHIPPING_FEDEXEU_STEP_BOX_70_3_TITLE' , 'Erhöhungszuschlag bis 70 kg BOX');
define('MODULE_SHIPPING_FEDEXEU_STEP_BOX_70_3_DESC' , 'Erhöhungszuschlag pro übersteigende 0,50 kg in EUR');
define('MODULE_SHIPPING_FEDEXEU_COUNTRIES_4_TITLE' , 'Weltzone A Länder');
define('MODULE_SHIPPING_FEDEXEU_COUNTRIES_4_DESC' , 'Durch Komma getrennt Liste der Länder als zwei Zeichen ISO-Code Landeskennzahlen, die Teil der Zone A sind.');
define('MODULE_SHIPPING_FEDEXEU_COST_PAK_4_TITLE' , 'Tariftabelle für Zone A bis 2.50 kg PAK');
define('MODULE_SHIPPING_FEDEXEU_COST_PAK_4_DESC' , 'Tarif Tabelle für die Zone A, basiered auf <b>\'PAK\'</b> bis 2.50 kg Versandgewicht.');
define('MODULE_SHIPPING_FEDEXEU_COST_BOX_4_TITLE' , 'Tariftabelle für Zone A bis 10 kg BOX');
define('MODULE_SHIPPING_FEDEXEU_COST_BOX_4_DESC' , 'Tarif Tabelle für die Zone A, basiered auf <b>\'BOX\'</b> bis 10 kg Versandgewicht.');
define('MODULE_SHIPPING_FEDEXEU_STEP_BOX_20_4_TITLE' , 'Erhöhungszuschlag bis 20 kg BOX');
define('MODULE_SHIPPING_FEDEXEU_STEP_BOX_20_4_DESC' , 'Erhöhungszuschlag pro übersteigende 0,50 kg in EUR');
define('MODULE_SHIPPING_FEDEXEU_STEP_BOX_40_4_TITLE' , 'Erhöhungszuschlag bis 40 kg BOX');
define('MODULE_SHIPPING_FEDEXEU_STEP_BOX_40_4_DESC' , 'Erhöhungszuschlag pro übersteigende 0,50 kg in EUR');
define('MODULE_SHIPPING_FEDEXEU_STEP_BOX_70_4_TITLE' , 'Erhöhungszuschlag bis 70 kg BOX');
define('MODULE_SHIPPING_FEDEXEU_STEP_BOX_70_4_DESC' , 'Erhöhungszuschlag pro übersteigende 0,50 kg in EUR');
define('MODULE_SHIPPING_FEDEXEU_COUNTRIES_5_TITLE' , 'Weltzone B Länder');
define('MODULE_SHIPPING_FEDEXEU_COUNTRIES_5_DESC' , 'Durch Komma getrennt Liste der Länder als zwei Zeichen ISO-Code Landeskennzahlen, die Teil der Zone B sind.');
define('MODULE_SHIPPING_FEDEXEU_COST_PAK_5_TITLE' , 'Tariftabelle für Zone B bis 2.50 kg PAK');
define('MODULE_SHIPPING_FEDEXEU_COST_PAK_5_DESC' , 'Tarif Tabelle für die Zone B, basiered auf <b>\'PAK\'</b> bis 2.50 kg Versandgewicht.');
define('MODULE_SHIPPING_FEDEXEU_COST_BOX_5_TITLE' , 'Tariftabelle für Zone B bis 10 kg BOX');
define('MODULE_SHIPPING_FEDEXEU_COST_BOX_5_DESC' , 'Tarif Tabelle für die Zone B, basiered auf <b>\'BOX\'</b> bis 10 kg Versandgewicht.');
define('MODULE_SHIPPING_FEDEXEU_STEP_BOX_20_5_TITLE' , 'Erhöhungszuschlag bis 20 kg BOX');
define('MODULE_SHIPPING_FEDEXEU_STEP_BOX_20_5_DESC' , 'Erhöhungszuschlag pro übersteigende 0,50 kg in EUR');
define('MODULE_SHIPPING_FEDEXEU_STEP_BOX_40_5_TITLE' , 'Erhöhungszuschlag bis 40 kg BOX');
define('MODULE_SHIPPING_FEDEXEU_STEP_BOX_40_5_DESC' , 'Erhöhungszuschlag pro übersteigende 0,50 kg in EUR');
define('MODULE_SHIPPING_FEDEXEU_STEP_BOX_70_5_TITLE' , 'Erhöhungszuschlag bis 70 kg BOX');
define('MODULE_SHIPPING_FEDEXEU_STEP_BOX_70_5_DESC' , 'Erhöhungszuschlag pro übersteigende 0,50 kg in EUR');
define('MODULE_SHIPPING_FEDEXEU_COUNTRIES_6_TITLE' , 'Weltzone C Länder');
define('MODULE_SHIPPING_FEDEXEU_COUNTRIES_6_DESC' , 'Durch Komma getrennt Liste der Länder als zwei Zeichen ISO-Code Landeskennzahlen, die Teil der Zone C sind.');
define('MODULE_SHIPPING_FEDEXEU_COST_PAK_6_TITLE' , 'Tariftabelle für Zone C bis 2.50 kg PAK');
define('MODULE_SHIPPING_FEDEXEU_COST_PAK_6_DESC' , 'Tarif Tabelle für die Zone C, basiered auf <b>\'PAK\'</b> bis 2.50 kg Versandgewicht.');
define('MODULE_SHIPPING_FEDEXEU_COST_BOX_6_TITLE' , 'Tariftabelle für Zone C bis 10 kg BOX');
define('MODULE_SHIPPING_FEDEXEU_COST_BOX_6_DESC' , 'Tarif Tabelle für die Zone C, basiered auf <b>\'BOX\'</b> bis 10 kg Versandgewicht.');
define('MODULE_SHIPPING_FEDEXEU_STEP_BOX_20_6_TITLE' , 'Erhöhungszuschlag bis 20 kg BOX');
define('MODULE_SHIPPING_FEDEXEU_STEP_BOX_20_6_DESC' , 'Erhöhungszuschlag pro übersteigende 0,50 kg in EUR');
define('MODULE_SHIPPING_FEDEXEU_STEP_BOX_40_6_TITLE' , 'Erhöhungszuschlag bis 40 kg BOX');
define('MODULE_SHIPPING_FEDEXEU_STEP_BOX_40_6_DESC' , 'Erhöhungszuschlag pro übersteigende 0,50 kg in EUR');
define('MODULE_SHIPPING_FEDEXEU_STEP_BOX_70_6_TITLE' , 'Erhöhungszuschlag bis 70 kg BOX');
define('MODULE_SHIPPING_FEDEXEU_STEP_BOX_70_6_DESC' , 'Erhöhungszuschlag pro übersteigende 0,50 kg in EUR');
define('MODULE_SHIPPING_FEDEXEU_COUNTRIES_7_TITLE' , 'Weltzone D Länder');
define('MODULE_SHIPPING_FEDEXEU_COUNTRIES_7_DESC' , 'Durch Komma getrennt Liste der Länder als zwei Zeichen ISO-Code Landeskennzahlen, die Teil der Zone D sind.');
define('MODULE_SHIPPING_FEDEXEU_COST_PAK_7_TITLE' , 'Tariftabelle für Zone D bis 2.50 kg PAK');
define('MODULE_SHIPPING_FEDEXEU_COST_PAK_7_DESC' , 'Tarif Tabelle für die Zone D, basiered auf <b>\'PAK\'</b> bis 2.50 kg Versandgewicht.');
define('MODULE_SHIPPING_FEDEXEU_COST_BOX_7_TITLE' , 'Tariftabelle für Zone D bis 10 kg BOX');
define('MODULE_SHIPPING_FEDEXEU_COST_BOX_7_DESC' , 'Tarif Tabelle für die Zone D, basiered auf <b>\'BOX\'</b> bis 10 kg Versandgewicht.');
define('MODULE_SHIPPING_FEDEXEU_STEP_BOX_20_7_TITLE' , 'Erhöhungszuschlag bis 20 kg BOX');
define('MODULE_SHIPPING_FEDEXEU_STEP_BOX_20_7_DESC' , 'Erhöhungszuschlag pro übersteigende 0,50 kg in EUR');
define('MODULE_SHIPPING_FEDEXEU_STEP_BOX_40_7_TITLE' , 'Erhöhungszuschlag bis 40 kg BOX');
define('MODULE_SHIPPING_FEDEXEU_STEP_BOX_40_7_DESC' , 'Erhöhungszuschlag pro übersteigende 0,50 kg in EUR');
define('MODULE_SHIPPING_FEDEXEU_STEP_BOX_70_7_TITLE' , 'Erhöhungszuschlag bis 70 kg BOX');
define('MODULE_SHIPPING_FEDEXEU_STEP_BOX_70_7_DESC' , 'Erhöhungszuschlag pro übersteigende 0,50 kg in EUR');
define('MODULE_SHIPPING_FEDEXEU_COUNTRIES_8_TITLE' , 'Weltzone E Länder');
define('MODULE_SHIPPING_FEDEXEU_COUNTRIES_8_DESC' , 'Durch Komma getrennt Liste der Länder als zwei Zeichen ISO-Code Landeskennzahlen, die Teil der Zone E sind.');
define('MODULE_SHIPPING_FEDEXEU_COST_PAK_8_TITLE' , 'Tariftabelle für Zone E bis 2.50 kg PAK');
define('MODULE_SHIPPING_FEDEXEU_COST_PAK_8_DESC' , 'Tarif Tabelle für die Zone E, basiered auf <b>\'PAK\'</b> bis 2.50 kg Versandgewicht.');
define('MODULE_SHIPPING_FEDEXEU_COST_BOX_8_TITLE' , 'Tariftabelle für Zone E bis 10 kg BOX');
define('MODULE_SHIPPING_FEDEXEU_COST_BOX_8_DESC' , 'Tarif Tabelle für die Zone E, basiered auf <b>\'BOX\'</b> bis 10 kg Versandgewicht.');
define('MODULE_SHIPPING_FEDEXEU_STEP_BOX_20_8_TITLE' , 'Erhöhungszuschlag bis 20 kg BOX');
define('MODULE_SHIPPING_FEDEXEU_STEP_BOX_20_8_DESC' , 'Erhöhungszuschlag pro übersteigende 0,50 kg in EUR');
define('MODULE_SHIPPING_FEDEXEU_STEP_BOX_40_8_TITLE' , 'Erhöhungszuschlag bis 30 kg BOX');
define('MODULE_SHIPPING_FEDEXEU_STEP_BOX_40_8_DESC' , 'Erhöhungszuschlag pro übersteigende 0,50 kg in EUR');
define('MODULE_SHIPPING_FEDEXEU_STEP_BOX_70_8_TITLE' , 'Erhöhungszuschlag bis 50 kg BOX');
define('MODULE_SHIPPING_FEDEXEU_STEP_BOX_70_8_DESC' , 'Erhöhungszuschlag pro übersteigende 0,50 kg in EUR');
?>

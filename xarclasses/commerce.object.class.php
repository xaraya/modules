<?php
  /**************************************************************************\
  * CK-Ledger (running on top of phpgroupware)                               *
  * Written by CK Wu [ckwu@cheerful.com]                                     *
  * -----------------------------------------------                          *
  * xarLedger (running as a Xaraya module)                                   *
  * adapted by Marc Lutolf (marcinmilan@xaraya.com)                          *
  * -----------------------------------------------                          *
  *  This program is free software; you can redistribute it and/or modify it *
  *  under the terms of the GNU General Public License as published by the   *
  *  Free Software Foundation; either version 2 of the License, or (at your  *
  *  option) any later version.                                              *
  \**************************************************************************/

include_once 'modules/xen/xarclasses/xenobject.class.php';

class xenCommerceObject extends xenObject
{
    var $xmlobjectname = '';
    var $id;
    var $name;
    var $description;
    var $notes;
    var $start         = 0;
    var $end           = 0;
    var $astate        = 1;
    var $total         = 0;

//---------------------------------------------------------
// Constructor
//---------------------------------------------------------
    function xenCommerceObject()
    {
        parent::xenObject();

        $xentable =& xarDBGetTables();
        $commerce_address_book = xarDBGetSiteTablePrefix() . '_commerce_address_book';
        $commerce_customers_memo = xarDBGetSiteTablePrefix() . '_commerce_customers_memo';
        $commerce_products_xsell = xarDBGetSiteTablePrefix() . '_commerce_products_xsell';
        $commerce_address_format = xarDBGetSiteTablePrefix() . '_commerce_address_format';
        $commerce_admin_access = xarDBGetSiteTablePrefix() . '_commerce_admin_access';
        $commerce_banktransfer = xarDBGetSiteTablePrefix() . '_commerce_banktransfer';
        $commerce_banners = xarDBGetSiteTablePrefix() . '_commerce_banners';
        $commerce_banners_history = xarDBGetSiteTablePrefix() . '_commerce_banners_history';
        $commerce_categories = xarDBGetSiteTablePrefix() . '_commerce_categories';
        $commerce_categories_description = xarDBGetSiteTablePrefix() . '_commerce_categories_description';
        $commerce_configuration = xarDBGetSiteTablePrefix() . '_commerce_configuration';
        $commerce_configuration_group = xarDBGetSiteTablePrefix() . '_commerce_configuration_group';
        $commerce_counter = xarDBGetSiteTablePrefix() . '_commerce_counter';
        $commerce_counter_history = xarDBGetSiteTablePrefix() . '_commerce_counter_history';
        $commerce_countries = xarDBGetSiteTablePrefix() . '_commerce_countries';
        $commerce_currencies = xarDBGetSiteTablePrefix() . '_commerce_currencies';
        $commerce_customers = xarDBGetSiteTablePrefix() . '_commerce_customers';
        $commerce_customers_basket = xarDBGetSiteTablePrefix() . '_commerce_customers_basket';
        $commerce_customers_basket_attributes = xarDBGetSiteTablePrefix() . '_commerce_customers_basket_attributes';
        $commerce_customers_info = xarDBGetSiteTablePrefix() . '_commerce_customers_info';
        $commerce_customers_ip = xarDBGetSiteTablePrefix() . '_commerce_customers_ip';
        $commerce_customers_status = xarDBGetSiteTablePrefix() . '_commerce_customers_status';
        $commerce_customers_status_history = xarDBGetSiteTablePrefix() . '_commerce_customers_status_history';
        $commerce_languages = xarDBGetSiteTablePrefix() . '_commerce_languages';
        $commerce_manufacturers = xarDBGetSiteTablePrefix() . '_commerce_manufacturers';
        $commerce_manufacturers_info = xarDBGetSiteTablePrefix() . '_commerce_manufacturers_info';
        $commerce_newsletters = xarDBGetSiteTablePrefix() . '_commerce_newsletters';
        $commerce_newsletters_history = xarDBGetSiteTablePrefix() . '_commerce_newsletters_history';
        $commerce_orders = xarDBGetSiteTablePrefix() . '_commerce_orders';
        $commerce_orders_products = xarDBGetSiteTablePrefix() . '_commerce_orders_products';
        $commerce_orders_status = xarDBGetSiteTablePrefix() . '_commerce_orders_status';
        $commerce_orders_status_history = xarDBGetSiteTablePrefix() . '_commerce_orders_status_history';
        $commerce_orders_products_attributes = xarDBGetSiteTablePrefix() . '_commerce_orders_products_attributes';
        $commerce_orders_products_download = xarDBGetSiteTablePrefix() . '_commerce_orders_products_download';
        $commerce_orders_total = xarDBGetSiteTablePrefix() . '_commerce_orders_total';
        $commerce_products = xarDBGetSiteTablePrefix() . '_commerce_products';
        $commerce_products_attributes = xarDBGetSiteTablePrefix() . '_commerce_products_attributes';
        $commerce_products_attributes_download = xarDBGetSiteTablePrefix() . '_commerce_products_attributes_download';
        $commerce_products_description = xarDBGetSiteTablePrefix() . '_commerce_products_description';
        $commerce_products_notifications = xarDBGetSiteTablePrefix() . '_commerce_products_notifications';
        $commerce_products_options = xarDBGetSiteTablePrefix() . '_commerce_products_options';
        $commerce_products_options_values = xarDBGetSiteTablePrefix() . '_commerce_products_options_values';
        $commerce_products_options_values_to_products_options = xarDBGetSiteTablePrefix() . '_commerce_products_options_values_to_products_options';
        $commerce_products_graduated_prices = xarDBGetSiteTablePrefix() . '_commerce_products_graduated_prices';
        $commerce_products_to_categories = xarDBGetSiteTablePrefix() . '_commerce_products_to_categories';
        $commerce_reviews = xarDBGetSiteTablePrefix() . '_commerce_reviews';
        $commerce_reviews_description = xarDBGetSiteTablePrefix() . '_commerce_reviews_description';
        $commerce_sessions = xarDBGetSiteTablePrefix() . '_commerce_sessions';
        $commerce_specials = xarDBGetSiteTablePrefix() . '_commerce_specials';
        $commerce_tax_class = xarDBGetSiteTablePrefix() . '_commerce_tax_class';
        $commerce_tax_rates = xarDBGetSiteTablePrefix() . '_commerce_tax_rates';
        $commerce_geo_zones = xarDBGetSiteTablePrefix() . '_commerce_geo_zones';
        $commerce_whos_online = xarDBGetSiteTablePrefix() . '_commerce_whos_online';
        $commerce_zones = xarDBGetSiteTablePrefix() . '_commerce_zones';
        $commerce_zones_to_geo_zones = xarDBGetSiteTablePrefix() . '_commerce_zones_to_geo_zones';
        $commerce_box_align = xarDBGetSiteTablePrefix() . '_commerce_box_align';
        $commerce_content_manager = xarDBGetSiteTablePrefix() . '_commerce_content_manager';
        $commerce_media_content = xarDBGetSiteTablePrefix() . '_commerce_media_content';
        $commerce_products_content = xarDBGetSiteTablePrefix() . '_commerce_products_content';
        $commerce_module_newsletter = xarDBGetSiteTablePrefix() . '_commerce_module_newsletter';
        $commerce_cm_file_flags = xarDBGetSiteTablePrefix() . '_commerce_cm_file_flags';

        $xentable['commerce_address_book'] = $commerce_address_book;
        $xentable['commerce_customers_memo'] = $commerce_customers_memo;
        $xentable['commerce_products_xsell'] = $commerce_products_xsell;
        $xentable['commerce_address_format'] = $commerce_address_format;
        $xentable['commerce_admin_access'] = $commerce_admin_access;
        $xentable['commerce_banktransfer'] = $commerce_banktransfer;
        $xentable['commerce_banners'] = $commerce_banners;
        $xentable['commerce_banners_history'] = $commerce_banners_history;
        $xentable['commerce_categories'] = $commerce_categories;
        $xentable['commerce_categories_description'] = $commerce_categories_description;
        $xentable['commerce_configuration'] = $commerce_configuration;
        $xentable['commerce_configuration_group'] = $commerce_configuration_group;
        $xentable['commerce_counter'] = $commerce_counter;
        $xentable['commerce_counter_history'] = $commerce_counter_history;
        $xentable['commerce_countries'] = $commerce_countries;
        $xentable['commerce_currencies'] = $commerce_currencies;
        $xentable['commerce_customers'] = $commerce_customers;
        $xentable['commerce_customers_basket'] = $commerce_customers_basket;
        $xentable['commerce_customers_basket_attributes'] = $commerce_customers_basket_attributes;
        $xentable['commerce_customers_info'] = $commerce_customers_info;
        $xentable['commerce_customers_ip'] = $commerce_customers_ip;
        $xentable['commerce_customers_status'] = $commerce_customers_status;
        $xentable['commerce_customers_status_history'] = $commerce_customers_status_history;
        $xentable['commerce_languages'] = $commerce_languages;
        $xentable['commerce_manufacturers'] = $commerce_manufacturers;
        $xentable['commerce_manufacturers_info'] = $commerce_manufacturers_info;
        $xentable['commerce_newsletters'] = $commerce_newsletters;
        $xentable['commerce_newsletters_history'] = $commerce_newsletters_history;
        $xentable['commerce_orders'] = $commerce_orders;
        $xentable['commerce_orders_products'] = $commerce_orders_products;
        $xentable['commerce_orders_status'] = $commerce_orders_status;
        $xentable['commerce_orders_status_history'] = $commerce_orders_status_history;
        $xentable['commerce_orders_products_attributes'] = $commerce_orders_products_attributes;
        $xentable['commerce_orders_products_download'] = $commerce_orders_products_download;
        $xentable['commerce_orders_total'] = $commerce_orders_total;
        $xentable['commerce_products'] = $commerce_products;
        $xentable['commerce_products_attributes'] = $commerce_products_attributes;
        $xentable['commerce_products_attributes_download'] = $commerce_products_attributes_download;
        $xentable['commerce_products_description'] = $commerce_products_description;
        $xentable['commerce_products_notifications'] = $commerce_products_notifications;
        $xentable['commerce_products_options'] = $commerce_products_options;
        $xentable['commerce_products_options_values'] = $commerce_products_options_values;
        $xentable['commerce_products_options_values_to_products_options'] = $commerce_products_options_values_to_products_options;
        $xentable['commerce_products_graduated_prices'] = $commerce_products_graduated_prices;
        $xentable['commerce_products_to_categories'] = $commerce_products_to_categories;
        $xentable['commerce_reviews'] = $commerce_reviews;
        $xentable['commerce_reviews_description'] = $commerce_reviews_description;
        $xentable['commerce_sessions'] = $commerce_sessions;
        $xentable['commerce_specials'] = $commerce_specials;
        $xentable['commerce_tax_class'] = $commerce_tax_class;
        $xentable['commerce_tax_rates'] = $commerce_tax_rates;
        $xentable['commerce_geo_zones'] = $commerce_geo_zones;
        $xentable['commerce_whos_online'] = $commerce_whos_online;
        $xentable['commerce_zones'] = $commerce_zones;
        $xentable['commerce_zones_to_geo_zones'] = $commerce_zones_to_geo_zones;
        $xentable['commerce_box_align'] = $commerce_box_align;
        $xentable['commerce_content_manager'] = $commerce_content_manager;
        $xentable['commerce_media_content'] = $commerce_media_content;
        $xentable['commerce_products_content'] = $commerce_products_content;
        $xentable['commerce_module_newsletter'] = $commerce_module_newsletter;
        $xentable['commerce_cm_file_flags'] = $commerce_cm_file_flags;
    }

//---------------------------------------------------------
// Get a list of items ids
//---------------------------------------------------------

/*    function getidlist($querymainstring, $queryconditionalstring='',$args)
{

        $q = new xarQueery();
        $query = $querymainstring;
        if (!stristr($querymainstring,'WHERE')) $query .= " WHERE 1 = 1 ";

        foreach ($args as $key => $value) {
            if($key == 'startnum') {$q->setstartat($value);}
            elseif ($key == 'numitems') {$q->setrowstodo($value);}
            elseif ($key == 'order') {$order = $value;}
            elseif ($key == 'sort') {$sort = $value;}
            elseif ($key == 'filter') {$query .= $value;}
            elseif ($key == 'timeperiod') {$this->settimeperiod($value);}
            else {
                $query .= "AND " . $key . " = " . xarVarPrepForStore($value);
            }
        }

        $query .= " " . $queryconditionalstring;
        if(isset($order)) $query .= " ORDER BY " . $order . " " . $sort;

        if (!$q->run($query,0)) return;

        $items = array();
        while(!$q->result->EOF) {
            list($this->id) = $q->result->fields;
            $items[] = $this->id;
            $q->result->MoveNext();
        }
        $this->settotal(count($items));
        return $items;
    }
    */
    function getidlist(&$q)
    {
//        echo $q->getstatement();exit;
        $q->open();
        $q->run();
        $items = array();
        foreach ($q->output() as $out)
            $items[] = array_pop($out);
        $this->settotal($q->getrows());
        return $items;
    }

//---------------------------------------------------------
// Post to the database
//---------------------------------------------------------
    function post($args,$op,$logop='')
//    function post($args,$query,$op,$logop='')
    {
        $userid = xarSessionGetVar('uid');
        $date = date("Ymd") ;
        $time = date("H:i:s") ;
        $logentry = new xarQuery("INSERT",
                     array($this->logtable),
                     array(
                        array('name' => 'trans_id', 'value' => $this->id),
                        array('name' => 'userid', 'value' => $userid),
                        array('name' => 'date', 'value' => $date),
                        array('name' => 'time', 'value' => $time)
                     )
                    );
        $logentry->addfields($args);
        $logentry->addfield('op',$logop);
        if (!$logentry->run()) return;

        if ($op == "new") {
            $dbconn = $logentry->getconnection();
            $this->id = $dbconn->PO_Insert_ID($this->logtable,'id');
            $logentry = new xarQuery("UPDATE",
                         array($this->logtable),
                         array(
                            array('name' => 'trans_id', 'value' => $this->id)
                         )
                        );
            $logentry->eq('id',$this->id);
            if (!$logentry->run()) return;
//            $query->addfield(array('name' => 'id', 'value' => $this->id));
        }
//        if (!$query->run()) return;
        $actioned = $this->id;
        return $actioned;
  }

// Create an XML ledger object
//---------------------------------------------------------
    function getxmlobject($objectname='')
    {
        if($objectname == '') $objectname = $this->xmlobjectname;

        if (!$this->getxmlschema()) return;

        $xml  = '<ledgerobject name="' . $objectname . '">' . "\n";
        foreach ($this->getxmlschema() as $key => $value) {
            $xml .= '    <' . $key . '>';
            $xml .= $this->{$value};
            $xml .= '</' . $key . '>' . "\n";
        }
        $xml .= '</ledgerobject>';
        return $xml;
    }


// Send back the object titles
//---------------------------------------------------------
    function recalltitles($data)
    {
        $objectdata = array('lang_id' => "ID"
        );
        foreach ($data as $datumkey => $datumvalue) $objectdata[$datumkey] = $datumvalue;
        return $data;
    }

// Send back the object data
//---------------------------------------------------------
    function recalldata($data)
    {
        $objectdata = array('id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'notes' => $this->notes,
            'start' => $this->start,
            'end' => $this->end,
            'active' => $this->astate,
            'total' => $this->total
        );
        foreach ($data as $datumkey => $datumvalue) $objectdata[$datumkey] = $datumvalue;
        return $objectdata;
    }


// Send back the object dropdown
//---------------------------------------------------------
    function dropdown($default=0,$filter=array())
    {
        $sl = new xarSelectList();
        $dropdown = '<OPTION value="0"></OPTION>';
        $dropdown .= $sl->getlist($this->customertable,'id',array('name'),$filter,$default);
        return $dropdown;
    }

// Sets and gets
//---------------------------------------------------------
    function getID()
    {
        return $this->id;
    }
    function getname()
    {
        return $this->name;
    }
    function gettotal()
    {
        return $this->total;
    }
    function getxmlschema()
    {
        return;
    }

    function settotal($x)
    {
        $this->total = $x;
    }

// Export an xml file
//---------------------------------------------------------
    function xmlexport($string,$filename='')
    {
        if ($filename == '') $filename = $this->xmlobjectname;
//        $filename = PMA_convert_string($convcharset, 'iso-8859-1', $filename);
        $ext       = 'xml';
        $mime_type = 'application/x-download';
        $now = gmdate('D, d M Y H:i:s') . ' GMT';

        // Send headers
        header('Content-Type: ' . $mime_type);
        header('Expires: ' . $now);
        // lem9 & loic1: IE need specific headers
        $sniff = xarModAPIFunc('sniffer','user','sniff');
        if (xarSessionGetVar('browsername') == 'Microsoft Internet Explorer') {
            header('Content-Disposition: inline; filename="' . $filename . '.' . $ext . '"');
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            header('Pragma: public');
        } else {
            header('Content-Disposition: attachment; filename="' . $filename . '.' . $ext . '"');
            header('Pragma: no-cache');
        }

        $crlf = "\n";
        $host = xarServerGetVar('HTTP_HOST');
        $host = preg_replace('/:.*/', '', $host);
        $head = '<?xml version="1.0" encoding="' . 'utf-8' . '"?>' . $crlf . $crlf;
        $head         .= '<!--' . $crlf
                             .  '- xarLedger XML-Output' . $crlf
                             .  '- version 1.0' . $crlf
                             .  '- http://www.xaraya.com/' . $crlf
                             .  '- from: ' . $host . $crlf;
        $head         .= '-->' . $crlf . $crlf;
        $buffer         = $head . $string;
//        $buffer         .= $crlf;
        echo $buffer;
        exit;
    }

// Import an XML object
//---------------------------------------------------------
    function xmlimport($object,$schema)
    {
        foreach ($object['children'] as $field) {
            $content = isset($field['content']) ? $field['content'] : '';
            $this->{$schema[$field['name']]} = $content;
        }
    }

//---------------------------------------------------------
// Object hierarchy stuff
//---------------------------------------------------------

/*    function getContentNames()
    {
        $keys =  array_keys(get_class_vars(get_class($this)));
        $names = array();
        foreach ($keys as $key) {
            if(substr($key, 0, 8) == 'xardata_') $names[] = substr($key,8);
        }
        return $names;
    }

    function getContent()
    {
        $vars = get_object_vars($this);
        $content = array();
        foreach ($vars as $key =>$value) {
            if(substr($key, 0, 8) == 'xardata_') $content[substr($key,8)] = $value;
        }
        return $content;
    }
*/
 }
?>
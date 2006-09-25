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

    }

//---------------------------------------------------------
// Get a list of items ids
//---------------------------------------------------------

    function getidlist(&$q)
    {
//        echo $q->getstatement();exit;
        $q->open();
        if(!$q->run()) return;
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
        sys::import('modules.roles.class.xarQuery');
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

 }
?>
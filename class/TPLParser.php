<?php
// $Id$
// ----------------------------------------------------------------------
// Xaraya eXtensible Management System
// Copyright (C) 2002 by the Xaraya Development Team.
// http://www.xaraya.org
// ----------------------------------------------------------------------
// Original Author of file: Marco Canini
// Purpose of file: Templates file parsing
// ----------------------------------------------------------------------

class TPLParser
{
    public $transEntries    = array();
    public $transKeyEntries = array();

    function TPLParser()
    {
    }

    function getTransEntries()
    {
        return $this->transEntries;
    }

    function getTransKeyEntries()
    {
        return $this->transKeyEntries;
    }

    function parse($filename)
    {
        if (!file_exists($filename)) return;
        $this->filename = $filename;
        $this->_fd = fopen($filename, 'r');
        if (!$this->_fd) {
            $msg = xarML('Cannot open the file #(1)',$filename);
            xarErrorSet(XAR_SYSTEM_EXCEPTION, 'UNABLE_TO_LOAD', new SystemException($msg));
            return;
        }
        
        $filestring = file_get_contents($filename);
        $filestring = preg_replace("/&xar([\-A-Za-z\d.]{2,41});/","xar-entity",$filestring);
        $reader = new XMLReader();
        $reader->xml($filestring);
        $nodes = array();
        $i = 0;
        while ($reader->read()) {
            $i++;
            if ($reader->nodeType == XMLReader::TEXT) {
               $string = $reader->value;
            }
            else if ($reader->name == "xar::mlstring") {
               $string = $reader->value;
            } else {
                continue;
            }
            $string = trim($string);
            $string = preg_replace('/[\t ]+/',' ',$string);
            $string = preg_replace('/\s*\n\s*/',"\n",$string);
            $this->transEntries[$string][] = array('line' => $i, 'file' => $this->filename);
        }
        $reader->close();

    }
}
?>
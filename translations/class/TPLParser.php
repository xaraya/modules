<?php
// $Id$
// ----------------------------------------------------------------------
// Xaraya eXtensible Management System
// Copyright (C) 2002 by the Xaraya Development Team.
// http://www.xaraya.org
// ----------------------------------------------------------------------
// Original Author of file: Marco Canini
// Purpose of file:
// ----------------------------------------------------------------------

//

class TPLParser
{
    var $transEntries = array();
    var $transKeyEntries = array();

    var $_fd;
    var $_offs;
    var $_pos;
    var $_len;
    var $_buf;
    var $_line;
    var $_token;
    var $_right;
    var $_string;
    var $tokenarray;
    var $lasttokenarray;
    var $endtokenarray;
    var $isfunctiontokenarray;
    var $iskeytokenarray;

    function TPLParser()
    {
        $this->tokenarray = array("<xar:mlstring>", "<xar:mlkey>", "xarML('", "xarMLByKey('", 'xarML("', 'xarMLByKey("');
        $this->endtokenarray = array(array("</xar:mlstring>"), array("</xar:mlkey>"), array("')","',"), array("')","',"), array('")','",'), array('")','",'));
        $this->isfunctiontokenarray = array(0, 0, 1, 1, 1, 1);
        $this->iskeytokenarray = array(0, 1, 0, 1, 0, 1);
        $this->strlentokenarray = array(14, 11, 7, 12, 7, 12);
        $this->strlenendtokenarray = array(15, 12, 2, 2, 2, 2);
    }

    function getTransEntries()
    {
        return $this->transEntries;
    }

    function getTransKeyEntries()
    {
        return $this->transKeyEntries;
    }

    function _get_token() 
    {
        $found = false;
        // if (defined('TPLPARSERDEBUG'))
           // printf("Getting line %d\n"."for %s token %d<br />\n", $this->_line, $this->_right?'end':'begin', $this->_token);
        $this->_pos = -1;
        foreach( $this->lasttokenarray as $n => $v )
        {
            $p = strpos( $this->_buf, $v, $this->_offs );
            if ($p===FALSE)
                continue;
            if (($p<$this->_pos)||($this->_pos==-1)) {
                $this->_pos = $p;
                $this->_token = $n;
            }
        }
        // list($this->_pos,$this->_token) = $this->findfirstof($this->_buf, $this->lasttokenarray, $this->_offs);
        if ($this->_pos != -1) {
            // if (defined('TPLPARSERDEBUG'))
                // printf("Found %s token %s[%d] at pos %d<br />\n", $this->_right?'end':'begin', htmlspecialchars(substr($this->_buf, $this->_pos, strlen($this->tokenarray[$this->_token]))), $this->_token, $this->_pos);
            if ($this->_right)
                $this->_string .= substr($this->_buf, $this->_offs, $this->_pos - $this->_offs);
            if ($this->_right)
                $this->_offs = $this->_pos + $this->strlenendtokenarray[$this->_token];
            else
                $this->_offs = $this->_pos + $this->strlentokenarray[$this->_token];
            if ($this->_offs > $this->_len) $this->_offs = $this->_len;
            $found = true;
            if(!$this->_right) {
                // ParseClose
                $this->_string ='';
                $this->_right = true;
                $this->lasttokenarray = $this->endtokenarray[$this->_token];
                if ($this->_get_token()) {
                    if (defined('TPLPARSERDEBUG'))
                       printf("Result: %s<br />\n", $this->_string);
                    if (!$this->isfunctiontokenarray[$this->_token])
                        $this->_string = trim($this->_string);
                    if ($this->iskeytokenarray[$this->_token]) {
                        if (!isset($this->transKeyEntries[$this->_string])) {
                            $this->transKeyEntries[$this->_string] = array();
                        }
                        $this->transKeyEntries[$this->_string][] = array('line' => $this->_line, 'file' => $this->filename);
                    } else {
                        if (!isset($this->transEntries[$this->_string])) {
                            $this->transEntries[$this->_string] = array();
                        }
                        $this->transEntries[$this->_string][] = array('line' => $this->_line, 'file' => $this->filename);
                    }
                    $this->lasttokenarray = $this->tokenarray;
                    $this->_right = false;
                    $this->_get_token();
                }
            }
        } else {
            $found = false;
            if ($this->_right) {
                $this->_string .= substr($this->_buf, $this->_offs);
                while (!feof($this->_fd)) {
                    $this->_buf = fgets($this->_fd, 1024);
                    $this->_len = strlen($this->_buf);
                    $this->_line++;
                    $this->_offs = 0;
                    if (!$this->_get_token()) continue;
                    $found = true;
                    break;
                }
            }
            // $this->_offs = $this->_len;
            $this->_offs = 0;
        }
        return $found;
    }

    function parse($filename)
    {
        if (!file_exists($filename)) return;
        $this->filename = $filename;
        $this->_fd = fopen($filename, 'r');
        if (!$this->_fd) {
            $msg = xarML('Cannot open the file #(1)',$filename);
            xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'UNABLE_TO_LOAD', new SystemException($msg));
            return;
        }
        if (!$filesize = filesize($filename)) return;

        $this->_offs = 0;
        $this->_len = 0;
        $this->_right = false;
        $this->lasttokenarray = $this->tokenarray;

        while (!feof($this->_fd)) {
            $this->_buf = fgets($this->_fd, 1024);
            $this->_len = strlen($this->_buf);
            $this->_line++;
            $this->_offs = 0;
            $this->_get_token();
        }

        fclose($this->_fd);
    }
}
?>
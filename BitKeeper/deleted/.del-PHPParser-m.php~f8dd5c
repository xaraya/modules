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

class PHPParser
{
    var $transEntries = array();
    var $transKeyEntries = array();
    var $includedFiles = array();
    var $parsedFiles = array();
    var $notToParseFiles = array();

    function PHPParser()
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
        $this->parseFile($filename);

        $this->parsedFiles[$filename] = true;
        $includedFiles = $this->includedFiles;
        $this->includedFiles = array();

        foreach($includedFiles as $ifilename => $t) {
            if (!isset($this->parsedFiles[$ifilename]) &&
                !isset($this->notToParseFiles[$ifilename])) {

                $this->parse($ifilename);
            }
        }
    }

    function parseFile($filename)
    {
        $line = 0;

        if (!file_exists($filename)) return;
//        echo file_exists($filename)
        $fd = fopen($filename, 'r');

        if (!$filesize = filesize($filename)) return;

        while (!feof($fd)) {
            $buf = fgets($fd, 1024);
            $line++;
            if (preg_match_all('!(xarML|xarMLByKey)\s*\(\s*\'(.*)\'[,|\)]!', $buf, $matches)) {
                foreach ($matches[2] as $match) {
//                    if ($string = $this->parseString($match)) {
                        $string = $match;
                        if (!isset($this->transEntries[$string])) {
                            $this->transEntries[$string] = array();
                        }
                        $this->transEntries[$string][] = array('line' => $line, 'file' => $filename);
                    }
//                }
            }
            if (preg_match_all('!(xarML|xarMLByKey)\s*\(\s*\"(.*)\"[,|\)]!', $buf, $matches)) {
                foreach ($matches[2] as $match) {
//                    if ($string = $this->parseString($match)) {
                      $string = $match;
                        if (!isset($this->transEntries[$string])) {
                            $this->transEntries[$string] = array();
                        }
                        $this->transEntries[$string][] = array('line' => $line, 'file' => $filename);
//                    }
                }
            } elseif (preg_match('!^\s*//\s*\{(ML_dont_parse|ML_include|ML_add_string|ML_add_key)\s*(.*)\}!', $buf, $match)) {
//                if ($string = $this->parseString($match[2])) {
                    $string = $match[2];
                    if ($match[1] == 'ML_dont_parse') {
                        $this->notToParseFiles[$string] = true;
                    } elseif ($match[1] == 'ML_include') {
                        $this->includedFiles[$string] = true;
                    } elseif ($match[1] == 'ML_add_string') {
                        if (!isset($this->transEntries[$string])) {
                            $this->transEntries[$string] = array();
                        }
                        $this->transEntries[$string][] = array('line' => $line, 'file' => $filename);
                    } elseif ($match[1] == 'ML_add_key') {
                        if (!isset($this->transKeyEntries[$string])) {
                            $this->transKeyEntries[$string] = array();
                        }
                        $this->transKeyEntries[$string][] = array('line' => $line, 'file' => $filename);
                    }
//                }
            }
            if (preg_match('!(include_once|include|require_once|require)\s*\(?\s*(.*)!', $buf, $match)) {
            //disregrd loggers
                $string = $match[2];
                if (!preg_match('!loggers!', $string, $match)) {
                    if (($string = $this->parseString($string)) && strpos($string, '$') === false) {
                        $this->includedFiles[$string] = true;
                    }
                }
            }
            if (defined('PHPPARSERDEBUG'))
                printf("Result: %s<br />\n", $string);
        }

        fclose($fd);
    }

    function parseString($buf)
    {
        $pos = 0;
        $len = strlen($buf);
        while ($pos < $len) {
            $char = $buf{$pos++};
            if ($char == "'" || $char == "'") {
                $quote = $char;
                break;
            } elseif ($char != ' ') {
                return;
            }
        }
        if ($pos == $len) return;
        $string = '';
        while ($pos < $len) {
            $char = $buf{$pos};
            if ($char == "\\") {
                if ($buf{$pos+1} == $quote) {
                    $string .= $quote;
                    $pos++;
                } else {
                    $string .= $char;
                }
            } else {
                if ($char == $quote) {
                    return $string;
                }
                $string .= $char;
            }
            $pos++;
        }
        return;
    }

}
?>
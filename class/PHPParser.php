<?php
/**
 * Translations Module
 *
 * @package modules
 * @subpackage translations module
 * @category Third Party Xaraya Module
 * @version 2.0.0
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://xaraya.com/index.php/release/77.html
 * @author Marco Canini
 */

class PHPParser
{
    public $transEntries = [];
    public $transKeyEntries = [];
    public $includedFiles = [];
    public $parsedFiles = [];
    public $notToParseFiles = [];

    public $_fd;
    public $_offs;
    public $_pos;
    public $_len;
    public $_buf;
    public $_line;
    public $_token;
    public $_right;
    public $_string;
    public $filename;

    public $tokenarray;
    public $endtokenarray;
    public $tokenarraytype;
    public $iskeytokenarray;
    public $strlentokenarray;
    public $strlenendtokenarray;
    public $lasttokenarray;

    public function __construct()
    {
        $this->tokenarray = ["xarML('", "xarMLS::translateByKey('", 'xarML("', 'xarMLS::translateByKey("', '{ML_dont_parse', '{ML_include', '{ML_add_string', '{ML_add_key', "include_once '", "include '", "require_once '", "require '", 'include_once "', 'include "', 'require_once "', 'require "', 'include_once(', 'include(', 'require_once(', 'require('];
        $this->endtokenarray = [["')","',"], ["')","',"], ['")','",'], ['")','",'], ['}'], ['}'], ['}'], ['}'], ["';"], ["';"], ["';"], ["';"], ['";'], ['";'], ['";'], ['";'], [');'], [');'], [');'], [');']];
        $this->tokenarraytype = ['function', 'function', 'function', 'function', 'ML_dont_parse', 'ML_include', 'ML_add_string', 'ML_add_key', 'include', 'include', 'include', 'include', 'include', 'include', 'include', 'include', 'include', 'include', 'include', 'include'];
        $this->iskeytokenarray = [0, 1, 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
        $this->strslasharray = [1, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
        $this->strlentokenarray = [7, 12, 7, 12, 14, 11, 14, 11, 14, 9, 14, 9, 14, 9, 14, 9, 13, 8, 13, 8];
        $this->strlenendtokenarray = [2, 2, 2, 2, 1, 1, 1, 1, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2];
    }

    public function getTransEntries()
    {
        return $this->transEntries;
    }

    public function getTransKeyEntries()
    {
        return $this->transKeyEntries;
    }

    public function _get_token()
    {
        $found = false;
        // if (defined('PHPPARSERDEBUG'))
        // printf("Getting line %d\n"."for %s token %d<br />\n", $this->_line, $this->_right?'end':'begin', $this->_token);
        $this->_pos = -1;
        foreach ($this->lasttokenarray as $n => $v) {
            $p = strpos($this->_buf, $v, $this->_offs);
            if ($p===false) {
                continue;
            }
            if (($p<$this->_pos)||($this->_pos==-1)) {
                $this->_pos = $p;
                if ($this->_right != true) {
                    $this->_token = $n;
                }
            }
        }
        if ($this->_pos != -1) {
            // if (defined('PHPPARSERDEBUG'))
            // printf("Found %s token %s[%d] at pos %d<br />\n", $this->_right?'end':'begin', htmlspecialchars(substr($this->_buf, $this->_pos, strlen($this->tokenarray[$this->_token]))), $this->_token, $this->_pos);
            if ($this->_right) {
                $this->_string .= substr($this->_buf, $this->_offs, $this->_pos - $this->_offs);
            }
            if ($this->_right) {
                $this->_offs = $this->_pos + $this->strlenendtokenarray[$this->_token];
            } else {
                $this->_offs = $this->_pos + $this->strlentokenarray[$this->_token];
            }
            if ($this->_offs > $this->_len) {
                $this->_offs = $this->_len;
            }
            $found = true;
            if (!$this->_right) {
                // ParseClose
                $this->_string ='';
                $this->_right = true;
                $this->lasttokenarray = $this->endtokenarray[$this->_token];
                $token = $this->_token;
                if ($this->_get_token()) {
                    // if (defined('PHPPARSERDEBUG'))
                    // printf("Result: %s<br />\n", $this->_string);
                    switch ($this->tokenarraytype[$token]) {
                        case 'function':
                            // Delete extra whitespaces and spaces around newline
                            $this->_string = trim($this->_string);
                            $this->_string = preg_replace('/[\t ]+/', ' ', $this->_string);
                            $this->_string = preg_replace('/\s*\n\s*/', "\n", $this->_string);
                            if ($this->strslasharray[$token]) {
                                $this->_string = str_replace('\\\'', '\'', $this->_string);
                            }
                            if ($this->iskeytokenarray[$token]) {
                                if (!isset($this->transKeyEntries[$this->_string])) {
                                    $this->transKeyEntries[$this->_string] = [];
                                }
                                $this->transKeyEntries[$this->_string][] = ['line' => $this->_line, 'file' => $this->filename];
                            } else {
                                if (!isset($this->transEntries[$this->_string])) {
                                    $this->transEntries[$this->_string] = [];
                                }
                                $this->transEntries[$this->_string][] = ['line' => $this->_line, 'file' => $this->filename];
                            }
                            break;
                        case 'ML_dont_parse':
                            $this->_string = trim($this->_string, " \t'");
                            $this->notToParseFiles[$this->_string] = true;
                            break;
                        case 'ML_include':
                            $this->_string = trim($this->_string, " \t'");
                            $this->includedFiles[$this->_string] = true;
                            break;
                        case 'ML_add_string':
                            $this->_string = trim($this->_string, " \t'");
                            if (!isset($this->transEntries[$this->_string])) {
                                $this->transEntries[$this->_string] = [];
                            }
                            $this->transEntries[$this->_string][] = ['line' => $line, 'file' => $filename];
                            break;
                        case 'ML_add_key':
                            $this->_string = trim($this->_string, " \t'");
                            if (!isset($this->transKeyEntries[$this->_string])) {
                                $this->transKeyEntries[$this->_string] = [];
                            }
                            $this->transKeyEntries[$this->_string][] = ['line' => $line, 'file' => $filename];
                            break;
                        case 'include':
                            $this->_string = trim($this->_string, " \t\"'");
                            //disregrd loggers
                            if (!preg_match('/loggers/', $this->_string, $match)) {
                                $this->includedFiles[$this->_string] = true;
                            }
                            break;
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
                    if (!$this->_get_token()) {
                        continue;
                    }
                    $found = true;
                    break;
                }
            }
            $this->_offs = 0;
        }
        return $found;
    }

    public function parse($filename)
    {
        $this->parseFile($filename);

        $this->parsedFiles[$filename] = true;
        $includedFiles = $this->includedFiles;
        $this->includedFiles = [];

        foreach ($includedFiles as $ifilename => $t) {
            if (!isset($this->parsedFiles[$ifilename]) &&
                !isset($this->notToParseFiles[$ifilename])) {
                $this->parse($ifilename);
            }
        }
    }

    public function parseFile($filename)
    {
        if (!file_exists($filename)) {
            return;
        }
        $this->filename = $filename;
        $this->_fd = fopen($filename, 'r');
        if (!$this->_fd) {
            $msg = xarML('Cannot open the file #(1)', $filename);
            throw new Exception($msg);
        }
        if (!$filesize = filesize($filename)) {
            return;
        }

        $this->_offs = 0;
        $this->_len = 0;
        $this->_right = false;
        $this->_line = 0;
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

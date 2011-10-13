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
            throw new Exception($msg);
        }
        
        $filestring = file_get_contents($filename);
        $filestring = preg_replace("/&xar([\-A-Za-z\d.]{2,41});/","xar-entity",$filestring);
        $reader = new XMLReader();
        $reader->xml($filestring);
        $nodes = array();
        $i = 0;

        while ($reader->read()) {
            $i++;
            
        // Ignore certain nodes            
            if ($reader->name == "xar:set" || $reader->name == "xar:comment" || $reader->name == "script" || $reader->name == "style") {
                if (!$reader->next()) break;
                $i++;
            }
            
            if ($reader->nodeType == XMLReader::TEXT) {
               $string = $reader->value;
            } else if ($reader->name == "xar:mlstring") {
               $string = $reader->value;
            } else {
                continue;
            }
            $string = trim($string);
            $string = preg_replace('/[\t ]+/',' ',$string);
            $string = preg_replace('/\s*\n\s*/',"\n",$string);

           // Ignore stuff enclosed by hashes
           if (substr($string,0,1) == '#') continue;
               
            $this->transEntries[$string][] = array('line' => $i, 'file' => $this->filename);
        }
        $reader->close();

    }
}
?>
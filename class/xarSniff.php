<?php
include_once('modules/sniffer/class/phpSniff.class.php');
class xarSniff extends phpSniff {
    function getname($type, $id = '')
    {
        switch ($type) {
            case 'browser':
                if (empty($id)) $id = $this->property('browser');
                $name = array_search(strtoupper($id), $this->_browsers);
                break;
            case 'os':
            // if(empty($id) $id = $this->property('os');
            // $name = 'os';
            // break;
            default:
                return false;
                break;
        } 
        $name = ucwords(strtolower($name));
        return $name;
    } 
} 

?>
<?php
/**
 * Sniffer System
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Sniffer Module
 * @link http://xaraya.com/index.php/release/775.html
 * @author Frank Besler using phpSniffer by Roger Raymond
 */
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
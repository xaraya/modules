<?php
/**
 *
 * Property Gmap
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2006 by to be added
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link to be added
 * @subpackage Gmaps Module
 * @author Marc Lutolf <mfl@netspan.ch>
 *
 */

include_once "modules/base/xarproperties/Dynamic_HTMLPage_Property.php";

class Gmap_Property extends Dynamic_HTMLPage_Property
{
    function __construct($args)
    {
        parent::__construct($args);
        $this->tplmodule = 'gmaps';
		$this->filepath   = 'modules/gmaps/xarproperties';
    }

    static function getRegistrationInfo()
    {
        $info = new PropertyRegistration();
        $info->reqmodules = array('gmaps');
        $info->id   = 30040;
        $info->name = 'gmap';
        $info->desc = 'Google Map';
        return $info;
    }
}
?>

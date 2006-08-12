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

class GoogleMap_Property extends Dynamic_Property
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

    function showInput($data = array())
    {
		$data['mapwidth']   = isset($data['mapwidth']) ? $data['mapwidth'] : xarModVars::get('gmaps', 'mapwidth');
		$data['mapheight']  = isset($data['mapheight']) ? $data['mapheight'] : xarModVars::get('gmaps', 'mapheight');
		$data['zoomlevel']  = isset($data['zoomlevel']) ? $data['zoomlevel'] : xarModVars::get('gmaps', 'zoomlevel');
		$data['latitude']   = isset($data['latitude']) ? $data['latitude'] : xarModVars::get('gmaps', 'centerlatitude');
		$data['longitude']  = isset($data['longitude']) ? $data['longitude'] : xarModVars::get('gmaps', 'centerlongitude');
		$data['gmapskey']   = isset($data['gmapskey']) ? $data['gmapskey'] : xarModVars::get('gmaps', 'gmapskey');

        return parent::showInput($data);
    }
    function showOutput($data = array())
    {
		$data['mapwidth']   = isset($data['mapwidth']) ? $data['mapwidth'] : xarModVars::get('gmaps', 'mapwidth');
		$data['mapheight']  = isset($data['mapheight']) ? $data['mapheight'] : xarModVars::get('gmaps', 'mapheight');
		$data['zoomlevel']  = isset($data['zoomlevel']) ? $data['zoomlevel'] : xarModVars::get('gmaps', 'zoomlevel');
		$data['latitude']   = isset($data['latitude']) ? $data['latitude'] : xarModVars::get('gmaps', 'centerlatitude');
		$data['longitude']  = isset($data['longitude']) ? $data['longitude'] : xarModVars::get('gmaps', 'centerlongitude');
		$data['gmapskey']   = isset($data['gmapskey']) ? $data['gmapskey'] : xarModVars::get('gmaps', 'gmapskey');

        return parent::showOutput($data);
    }
}
?>

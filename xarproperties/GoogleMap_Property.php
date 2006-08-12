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
        $info = xarRequestGetInfo();
        $regid = xarModGetIDFromName($info[0]);
		$data['mapwidth']   = isset($data['mapwidth']) ? $data['mapwidth'] : xarModUserVars::get('gmaps', 'mapwidth', $regid);
		$data['mapheight']  = isset($data['mapheight']) ? $data['mapheight'] : xarModUserVars::get('gmaps', 'mapheight', $regid);
		$data['zoomlevel']  = isset($data['zoomlevel']) ? $data['zoomlevel'] : xarModUserVars::get('gmaps', 'zoomlevel', $regid);
		$data['latitude']   = isset($data['latitude']) ? $data['latitude'] : xarModUserVars::get('gmaps', 'centerlatitude', $regid);
		$data['longitude']  = isset($data['longitude']) ? $data['longitude'] : xarModUserVars::get('gmaps', 'centerlongitude', $regid);
		$data['gmapskey']   = isset($data['gmapskey']) ? $data['gmapskey'] : xarModUserVars::get('gmaps', 'gmapskey', $regid);

        return parent::showInput($data);
    }
    function showOutput($data = array())
    {
        $info = xarRequestGetInfo();
        $regid = xarModGetIDFromName($info[0]);
		$data['mapwidth']   = isset($data['mapwidth']) ? $data['mapwidth'] : xarModUserVars::get('gmaps', 'mapwidth', $regid);
		$data['mapheight']  = isset($data['mapheight']) ? $data['mapheight'] : xarModUserVars::get('gmaps', 'mapheight', $regid);
		$data['zoomlevel']  = isset($data['zoomlevel']) ? $data['zoomlevel'] : xarModUserVars::get('gmaps', 'zoomlevel', $regid);
		$data['latitude']   = isset($data['latitude']) ? $data['latitude'] : xarModUserVars::get('gmaps', 'centerlatitude', $regid);
		$data['longitude']  = isset($data['longitude']) ? $data['longitude'] : xarModUserVars::get('gmaps', 'centerlongitude', $regid);
		$data['gmapskey']   = isset($data['gmapskey']) ? $data['gmapskey'] : xarModUserVars::get('gmaps', 'gmapskey', $regid);

        return parent::showOutput($data);
    }
}
?>

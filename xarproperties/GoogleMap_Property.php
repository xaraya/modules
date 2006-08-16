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
    private $regid = 30038;
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
        if (isset($data['module'])) {
			$this->regid = xarModGetIDFromName($data['module']);
        } else {
			$info = xarRequestGetInfo();
			$this->regid = xarModGetIDFromName($info[0]);
        }
		$data['mapwidth']   = isset($data['mapwidth']) ? $data['mapwidth'] : xarModUserVars::get('gmaps', 'mapwidth', $this->regid);
		$data['mapheight']  = isset($data['mapheight']) ? $data['mapheight'] : xarModUserVars::get('gmaps', 'mapheight', $this->regid);
		$data['zoomlevel']  = isset($data['zoomlevel']) ? $data['zoomlevel'] : xarModUserVars::get('gmaps', 'zoomlevel', $this->regid);
		$data['latitude']   = isset($data['latitude']) ? $data['latitude'] : xarModUserVars::get('gmaps', 'centerlatitude', $this->regid);
		$data['longitude']  = isset($data['longitude']) ? $data['longitude'] : xarModUserVars::get('gmaps', 'centerlongitude', $this->regid);
		$data['gmapskey']   = isset($data['gmapskey']) ? $data['gmapskey'] : xarModUserVars::get('gmaps', 'gmapskey', $this->regid);
		$data['locations'] = $this->getlocations($data);

        return parent::showInput($data);
    }
    function showOutput($data = array())
    {
        if (isset($data['module'])) {
			$this->regid = xarModGetIDFromName($data['module']);
        } else {
			$info = xarRequestGetInfo();
			$this->regid = xarModGetIDFromName($info[0]);
        }
		$data['mapwidth']   = isset($data['mapwidth']) ? $data['mapwidth'] : xarModUserVars::get('gmaps', 'mapwidth', $this->regid);
		$data['mapheight']  = isset($data['mapheight']) ? $data['mapheight'] : xarModUserVars::get('gmaps', 'mapheight', $this->regid);
		$data['zoomlevel']  = isset($data['zoomlevel']) ? $data['zoomlevel'] : xarModUserVars::get('gmaps', 'zoomlevel', $this->regid);
		$data['latitude']   = isset($data['latitude']) ? $data['latitude'] : xarModUserVars::get('gmaps', 'centerlatitude', $this->regid);
		$data['longitude']  = isset($data['longitude']) ? $data['longitude'] : xarModUserVars::get('gmaps', 'centerlongitude', $this->regid);
		$data['gmapskey']   = isset($data['gmapskey']) ? $data['gmapskey'] : xarModUserVars::get('gmaps', 'gmapskey', $this->regid);
		$data['locations'] = $this->getlocations($data);

        return parent::showOutput($data);
    }

    function getlocations($data = array())
    {
		if (isset($data['locations'])) {
			return $data['locations'];
		} else {
			$uselocations =  unserialize(xarModUserVars::get('gmaps', 'uselocations', $this->regid));
			$locations = array();
			return $locations;
		}
    }
}
?>

<?php
/**
 *
 * Property Gmap
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2006 by to be added
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link to be added
 * @subpackage Maps Module
 * @author Marc Lutolf <mfl@netspan.ch>
 *
 */

class GoogleMap_Property extends Dynamic_Property
{
    private $regid = 30038;
    function __construct($args)
    {
        parent::__construct($args);
        $this->tplmodule = 'maps';
        $this->filepath   = 'modules/maps/xarproperties';
    }

    static function getRegistrationInfo()
    {
        $info = new PropertyRegistration();
        $info->reqmodules = array('maps');
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
        $data['mapwidth']   = isset($data['mapwidth']) ? $data['mapwidth'] : xarModUserVars::get('maps', 'mapwidth', $this->regid);
        $data['mapheight']  = isset($data['mapheight']) ? $data['mapheight'] : xarModUserVars::get('maps', 'mapheight', $this->regid);
        $data['zoomlevel']  = isset($data['zoomlevel']) ? $data['zoomlevel'] : xarModUserVars::get('maps', 'zoomlevel', $this->regid);
        $data['latitude']   = isset($data['latitude']) ? $data['latitude'] : xarModUserVars::get('maps', 'centerlatitude', $this->regid);
        $data['longitude']  = isset($data['longitude']) ? $data['longitude'] : xarModUserVars::get('maps', 'centerlongitude', $this->regid);
        $data['mapskey']    = isset($data['mapskey']) ? $data['mapskey'] : xarModUserVars::get('maps', 'gmapskey', $this->regid);
        $data['locations']  = isset($data['locations']) ? $data['locations'] : $this->getlocations($data);

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
        $data['mapwidth']   = isset($data['mapwidth']) ? $data['mapwidth'] : xarModUserVars::get('maps', 'mapwidth', $this->regid);
        $data['mapheight']  = isset($data['mapheight']) ? $data['mapheight'] : xarModUserVars::get('maps', 'mapheight', $this->regid);
        $data['zoomlevel']  = isset($data['zoomlevel']) ? $data['zoomlevel'] : xarModUserVars::get('maps', 'zoomlevel', $this->regid);
        $data['latitude']   = isset($data['latitude']) ? $data['latitude'] : xarModUserVars::get('maps', 'centerlatitude', $this->regid);
        $data['longitude']  = isset($data['longitude']) ? $data['longitude'] : xarModUserVars::get('maps', 'centerlongitude', $this->regid);
        $data['mapskey']   = isset($data['mapskey']) ? $data['mapskey'] : xarModUserVars::get('maps', 'gmapskey', $this->regid);
        $data['locations'] = $this->getlocations($data);

        return parent::showOutput($data);
    }

    function getlocations($data = array())
    {
        if (isset($data['locations'])) {
            return $data['locations'];
        } else {
            $uselocations =  unserialize(xarModUserVars::get('maps', 'uselocations', $this->regid));
            $locations = array();
            if (in_array('dynamic',$uselocations)) {
                try {
                    $locations = array_merge($locations,xarModAPIFunc('maps','user','getlocations'));
                } catch(Exception $e) {
                }
            }
            if (in_array('module',$uselocations)) {
                try {
                    $locations = array_merge($locations,xarModAPIFunc(xarModGetNameFromID($this->regid),'user','getlocations'));
                } catch(Exception $e) {
                }
            }
            return $locations;
        }
    }
}
?>

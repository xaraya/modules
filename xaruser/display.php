<?php
/**
 *
 * Function display
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2006 by to be added
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link to be added
 * @subpackage Maps Module
 * @author Marc Lutolf <mfl@netspan.ch>
 * @author Brian Bain <xaraya@tefen.net>
 *
 * Purpose of file:  Display a Map
 *
 * @param to be added
 * @return to be added
 *
 */

function maps_user_display($args)
{
    extract($args);

    $data = array();

    $data['mapwidth']   = isset($args['mapwidth']) ? $args['mapwidth'] : xarModVars::get('maps', 'mapwidth');
    $data['mapheight']  = isset($args['mapheight']) ? $args['mapheight'] : xarModVars::get('maps', 'mapheight');
    $data['zoomlevel']  = isset($args['zoomlevel']) ? $args['zoomlevel'] : xarModVars::get('maps', 'zoomlevel');
    $data['latitude']   = isset($args['latitude']) ? $args['latitude'] : xarModVars::get('maps', 'latitude');
    $data['longitude']  = isset($args['longitude']) ? $args['longitude'] : xarModVars::get('maps', 'longitude');
    $data['mapskey']   = isset($args['mapskey']) ? $args['mapskey'] : xarModVars::get('maps', 'mapskey');

    if(!empty(id)){
        $data['location'] = xarModAPIFunc('maps','user','getlocation');
    }

    return $data;
}
?>
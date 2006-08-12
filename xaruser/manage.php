<?php
/**
 *
 * Function manage
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2006 by to be added
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link to be added
 * @subpackage Gmaps Module
 * @author Marc Lutolf <mfl@netspan.ch>
 * @author Brian Bain <xaraya@tefen.net>
 *
 * Purpose of file:  Manage and display a Google Map
 *
 * @param to be added
 * @return to be added
 *
 */

function gmaps_user_manage($args)
{
    extract($args);

    $data = array();

    $data['mapwidth']   = isset($args['mapwidth']) ? $args['mapwidth'] : xarModVars::get('gmaps', 'mapwidth');
    $data['mapheight']  = isset($args['mapheight']) ? $args['mapheight'] : xarModVars::get('gmaps', 'mapheight');
    $data['zoomlevel']  = isset($args['zoomlevel']) ? $args['zoomlevel'] : xarModVars::get('gmaps', 'zoomlevel');
    $data['latitude']   = isset($args['latitude']) ? $args['latitude'] : xarModVars::get('gmaps', 'latitude');
    $data['longitude']  = isset($args['longitude']) ? $args['longitude'] : xarModVars::get('gmaps', 'longitude');
    $data['gmapskey']   = isset($args['gmapskey']) ? $args['gmapskey'] : xarModVars::get('gmaps', 'gmapskey');

    return $data;
}
?>
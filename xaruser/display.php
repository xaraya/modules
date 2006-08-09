<?php
/**
 *
 * Function display
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2006 by to be added
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link to be added
 * @subpackage Gmaps Module
 * @author Marc Lutolf <mfl@netspan.ch>
 * @author Brian Bain <xaraya@tefen.net>
 *
 * Purpose of file:  Display a Google Map
 *
 * @param to be added
 * @return to be added
 *
 */

function gmaps_user_display($args)
{
    extract($args);

    $data = array();

    $data['mapwidth']   = isset($args['mapwidth']) ? $args['mapwidth'] : xarModGetVar('gmaps', 'mapwidth');
    $data['mapheight']  = isset($args['mapheight']) ? $args['mapheight'] : xarModGetVar('gmaps', 'mapheight');
    $data['zoomlevel']  = isset($args['zoomlevel']) ? $args['zoomlevel'] : xarModGetVar('gmaps', 'zoomlevel');
    $data['latitude']   = isset($args['latitude']) ? $args['latitude'] : xarModGetVar('gmaps', 'latitude');
    $data['longitude']  = isset($args['longitude']) ? $args['longitude'] : xarModGetVar('gmaps', 'longitude');
    $data['gmapskey']   = isset($args['gmapskey']) ? $args['gmapskey'] : xarModGetVar('gmaps', 'gmapskey');

    return $data;
}
?>
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

    $data['mapwidth']   = xarModGetVar('gmaps', 'mapwidth');
    $data['mapheight']  = xarModGetVar('gmaps', 'mapheight');
    $data['zoomlevel']  = xarModGetVar('gmaps', 'zoomlevel');
    $data['latitude']   = xarModGetVar('gmaps', 'latitude');
    $data['longitude']  = xarModGetVar('gmaps', 'longitude');
    $data['gmapskey']   = xarModGetVar('gmaps', 'gmapskey');

//TODO: Pull overriding values

    return $data;
}
?>
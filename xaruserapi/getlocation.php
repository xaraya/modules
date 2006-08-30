<?php
/**
 *
 * Function getlocation
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2006 by to be added
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link to be added
 * @subpackage Maps Module
 * @author Marc Lutolf <mfl@netspan.ch>
 *
 * Purpose of file:  Retrieves the data of a location
 *
 * @param to be added
 * @return mixed DD object item
 *
 */

sys::import('modules.xen.xarclasses.xenddquery');

function maps_userapi_getlocation($args)
{
    extract($args);

    $q = new xenDDQuery('maps_locations');
    if (isset($id)) $q->eq('id',$id);
    if (isset($name)) $q->eq('name',$name);
    if (!$q->run()) return;
    return $q->row();
}

?>
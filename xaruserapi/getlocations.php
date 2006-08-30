<?php
/**
 *
 * Function getlocations
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2006 by to be added
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link to be added
 * @subpackage Maps Module
 * @author Marc Lutolf <mfl@netspan.ch>
 *
 * Purpose of file:  Retrieves the data of set of locations
 *
 * @param to be added
 * @return mixed DD object item
 *
 */

sys::import('modules.xen.xarclasses.xenddquery');

function maps_userapi_getlocations($args)
{
    extract($args);

    if (isset($conditions)) {
    	$q = $conditions;
    } else {
	    $q = new xenDDQuery('maps_locations');
	}
	$q->addorder('name');
    if (!$q->run()) return;
    return $q->output();
}

?>
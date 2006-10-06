<?php
/**
 *
 * Property Map Location
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2006 by to be added
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link to be added
 * @subpackage Maps Module
 * @author Marc Lutolf <mfl@netspan.ch>
 *
 */

// We base it on the objectref property
sys::import('modules.dynamicdata.xarproperties.objectref');

/**
 * Handle the maplocation property
 *
 * @package maps
 */
class MapLocationProperty extends ObjectRefProperty
{
    public $id         = 30041;
    public $name       = 'maplocation';
    public $desc       = 'Map Location';
    public $reqmodules = array('math');

    // We explicitly use names here instead of id's, so we are independent of
    // how dd assigns them at a given time. Otherwise the validation is not
    // exportable to other sites.
    public $refobject    = 'maps_locations';    // Name of the object we want to reference
    public $store_prop   = 'id';   // Name of the property we want to use for storage
    public $display_prop = 'name';       // Name of the property we want to use for displaying.

    function __construct($args)
    {
        parent::__construct($args);
        $this->filepath   = 'modules/maps/xarproperties';
    }
}
?>

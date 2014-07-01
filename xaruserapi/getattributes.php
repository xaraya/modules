<?php
/**
 * EAV Module
 *
 * @package modules
 * @subpackage eav
 * @category Third Party Xaraya Module
 * @version 1.0.0
 * @copyright (C) 2013 Netspan AG
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @author Marc Lutolf <mfl@netspan.ch>
 */
/**
 * Get attributes for a specific object
 */
function eav_userapi_getattributes(Array $args=array())
{
    if (!isset($args['object_id'])) throw new BadParameterException('object_id');
    
    sys::import('xaraya.structures.query');
    $tables =& xarDB::getTables();

    $q = new Query('SELECT', $tables['eav_attributes']);
    $q->eq('object_id', (int)$args['object_id']);
    $q->setorder('seq');
    $q->run();

    sys::import('modules.dynamicdata.class.properties.master');
    $properties = array();
    $attributes = array();
    foreach ($q->output() as $row) {
        if (in_array($row['type'], array_keys($properties))) {
            $propobject = $properties[$row['type']];
        } else {
            $propobject = DataPropertyMaster::getProperty(array('type' => $row['type']));
            $properties[$row['type']] = $propobject;
        }
        $row['value'] = xarMod::apiFunc('eav', 'admin', 'getvalue', array('property' => $propobject, 'values' => $row));
        unset($row['default_tinyint']);
        unset($row['default_integer']);
        unset($row['default_decimal']);
        unset($row['default_string']);
        unset($row['default_text']);
        $attributes[$row['name']] = $row;
    }
    return $attributes;
}

?>

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

sys::import('modules.dynamicdata.class.objects.master');

/**
 * This is the pendant to DD's DataObjectMaster
 * It creates an object or objectlist that has as properties the attributes assigned to an EAV entity object
 *
 */

class EAVObjectMaster extends DataObjectMaster
{
    public static function getObject(array $args=array())
    {
        $args['propertyprefix'] = 'eav_';
        if (!isset($args['objectid'])) {
            $info = self::getObjectInfo($args);
            $args['objectid'] = $info['objectid'];
        }
        $args['parent_id'] = (int)$args['objectid'];
        unset($args['objectid']);

        $args['name'] = 'eav_empty';
        return parent::getObject($args);
    }

    public static function getObjectList(array $args=array())
    {
        $args['propertyprefix'] = 'eav_';
        if (!isset($args['objectid'])) {
            $info = self::getObjectInfo($args);
            $args['objectid'] = $info['objectid'];
        }
        $args['parent_id'] = (int)$args['objectid'];
        unset($args['objectid']);

        $args['name'] = 'eav_empty';
        return parent::getObjectList($args);
    }
}

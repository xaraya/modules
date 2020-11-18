<?php
/**
 * Karma Module
 *
 * @package modules
 * @subpackage karma
 * @category Third Party Xaraya Module
 * @version 1.0.0
 * @copyright (C) 2019 Luetolf-Carroll GmbH
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @author Marc Lutolf <marc@luetolf-carroll.com>
 */
/**
 * Get all tags
 *
 */
sys::import('modules.dynamicdata.class.objects.master');

function karma_userapi_getall_tags($args)
{
    $tag = DataObjectMaster::getObjectList(array('name' => 'karma_tags'));
    $items = $tag->getItems();
    return $items;
}

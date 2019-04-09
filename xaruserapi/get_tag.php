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
 * Get a tag item
 *
 */
sys::import('modules.dynamicdata.class.objects.master');

function karma_userapi_getall_tags($args)
{
    if(empty($args['itemid'])) die(xarML('No tag ID passed'));
    
    $tag = DataObjectMaster::getObject(array('name' => 'karma_tags'));
    $tag->getItem(array('itemid' => $args['itemid']));
    $tag_info = $tag->getFieldValues();
    return $tag_info;
}
?>
<?php
/**
 * Publications Module
 *
 * @package modules
 * @subpackage publications module
 * @category Third Party Xaraya Module
 * @version 2.0.0
 * @copyright (C) 2012 Netspan AG
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @author Marc Lutolf <mfl@netspan.ch>
 */

function publications_adminapi_save_version($args)
{
    if (empty($args['object'])) {
        throw new Exception(xarML('Missing object arg for saving version'));
    }

    sys::import('modules.dynamicdata.class.objects.master');
    $entries = DataObjectMaster::getObject(array('name' => 'publications_versions'));
    $entries->properties['content']->value = serialize($args['object']->getFieldValues(array(), 1));
    $entries->properties['operation']->value = $args['operation'];
    $entries->properties['version']->value = $args['object']->properties['version']->value;
    $entries->properties['page_id']->value = $args['object']->properties['id']->value;
    $entries->createItem();
    return true;
}

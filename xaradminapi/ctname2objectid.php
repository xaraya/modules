<?php
/**
 * @package modules
 * @copyright (C) 2002-2009 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage content
 * @link http://www.xaraya.com/index.php/release/1015.html
 * @author potion <potion@xaraya.com>
 */
/**
 *    Given a content_type name, return its object's objectid
 */
function content_adminapi_ctname2objectid($args) {

    $object = 'content_types';
    $id = 'itemid';

    extract($args);

    sys::import('modules.dynamicdata.class.objects.master');

    $list = DataObjectMaster::getObjectList(array('name' => $object));
    $filters = array(
        'where' => 'content_type eq \'' . $content_type . '\''
    );
    $items = $list->getItems($filters);
    if (empty($items)) {
        return false;
    } else {
        $item = end($items);
        return $item[$id];
    }

} 
?>
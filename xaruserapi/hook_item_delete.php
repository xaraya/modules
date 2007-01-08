<?php
/**
 * Subitems module
 *
 * @package modules
 * @copyright (C) 2002-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Subitems Module
 * @link http://xaraya.com/index.php/release/9356.html
 * @author Subitems Module Development Team
 */
/**
 * Delete a hooked item
 * @param module
 * @param itemtype
 * @param itemid
 */
function subitems_userapi_hook_item_delete($args)
{
    extract($args);
    // extrainfo -> module,itemtype,itemid
    if (!isset($extrainfo['module'])) {
        $extrainfo['module'] = xarModGetName();
    }
    if (empty($extrainfo['itemtype'])) {
        $extrainfo['itemtype'] = 0;
    }
    if (empty($extrainfo['itemid'])) {
        $extrainfo['itemid'] = $objectid;
    }

    // a object should be linked to this hook
    if(!$ddobjectlink = xarModAPIFunc('subitems','user','ddobjectlink_get',$extrainfo)) {
        return $extrainfo;
    }
    // nothing to see here
    if (empty($ddobjectlink)) return $extrainfo;

    // TODO: support multiple items too here? (perhaps not needed)
    $ddobjectlink = $ddobjectlink[0];

    $objectid = $ddobjectlink['objectid'];

    // get the Dynamic Object defined for this module (and itemtype, if relevant)
    $object = xarModAPIFunc('dynamicdata','user','getobject',
                             array('objectid' => $objectid,
                                     'status' => 1));
    if (!isset($object)) {
        return $extrainfo;
    }

    // get existing subitems
    $ids = xarModAPIFunc('subitems','user','dditems_getids',array('objectid' => $objectid,'itemid' => $extrainfo['itemid']));
    if(!isset($ids))
        return $extrainfo;

    // when itemids == array() => it will return all ids, but we don't want this
    if(count($ids) > 0)    {
       $items = xarModAPIFunc('dynamicdata',
                   'user',
                   'getitems',
                   array(
                         'modid' => $object->moduleid,
                         'itemtype' => $object->itemtype,
                         'itemids' => $ids
                         ));
    }
    else
        $items = Array();

    foreach($items as $ddid => $item)    {
        if(!xarModAPIFunc('dynamicdata','admin','delete',array(
                        'modid' => $object->moduleid,
                         'itemtype' => $object->itemtype,
                         'itemid' => $ddid
                         ))) return $extrainfo;

        // detach ids -> write db
        if(!xarModAPIFunc('subitems','admin','dditem_detach',array(
            'ddid' => $ddid,
            'objectid' => $objectid
            ))) return $extrainfo;
    }

 /* print "<pre>";
    print_r($object);
    print_r($items);
    print_r($ids);
    die("");   */

    return $extrainfo;
}

?>

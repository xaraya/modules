<?php
/**
 * Subitems module
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Subitems Module
 * @link http://xaraya.com/index.php/release/9356.html
 * @author Subitems Module Development Team
 */
/**
 * Hook to modify an item
 *
 * @return array
 */
function subitems_user_hook_item_modify($args)
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
    if(!$ddobjectlink = xarModAPIFunc('subitems','user','ddobjectlink_get',$extrainfo)) return '';
    // nothing to see here
    if (empty($ddobjectlink)) return '';

    $data = array();
    foreach($ddobjectlink as $index => $subobjectlink) {
        $subobjectid = $subobjectlink['objectid'];

        // get the Dynamic Object defined for this module (and itemtype, if relevant)
        $subobject = xarModAPIFunc('dynamicdata','user','getobject',
                                array('objectid' => $subobjectid, 'status' => 1));
        if (!isset($subobject)) return '';

        // get existing subitems
        $ids = xarModAPIFunc('subitems','user','dditems_getids',array('objectid' => $subobjectid,'itemid' => $extrainfo['itemid']));
        if(!isset($ids)) return '';

        if (!empty($subobjectlink['sort'])) {
            $sort = array();
            foreach ($subobjectlink['sort'] as $sortby => $sortmode) {
                $sort[] = "$sortby $sortmode";
            }
        } else {
            $sort = null;
        }

        // when itemids == array() => it will return all ids, but we don't want this
        if(count($ids) > 0)    {
        $items = xarModAPIFunc('dynamicdata','user','getitems',
                    array('modid' => $subobject->moduleid,
                          'itemtype' => $subobject->itemtype,
                         'itemids' => $ids,
                         'sort' => $sort
                         ));
        } else {
            $items = array();
        }

        $template = $subobject->name;
        if(!empty($subobjectlink['template']))
            $template = $subobjectlink['template'];

        // output
        $data['subitems'][$subobjectid]['properties'] =& $subobject->getProperties();
        $data['subitems'][$subobjectid]['values'] = $items;
        $data['subitems'][$subobjectid]['itemid'] = $extrainfo['itemid'];
        $data['subitems'][$subobjectid]['objectid'] = $subobjectid;
        $data['subitems'][$subobjectid]['object'] = $subobject;
        $data['subitems'][$subobjectid]['ids'] = $ids;
    }

    return xarTplModule('subitems','user','hook_item_modify',$data,$template);
}

?>

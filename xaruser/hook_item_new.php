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
 * Hook to add new subitem
 * @param array $extrainfo Array with info for hook
 * @return array
 */
function subitems_user_hook_item_new($args)
{
    extract($args);
    // extrainfo -> module,itemtype,itemid
    if (!isset($extrainfo['module'])) {
        $extrainfo['module'] = xarModGetName();
    }
    if (empty($extrainfo['itemtype'])) {
        $extrainfo['itemtype'] = 0;
    }

    // a object should be linked to this hook
    if(!$ddobjectlink = xarModAPIFunc('subitems','user','ddobjectlink_get',$extrainfo)) return '';
    // nothing to see here
    if (empty($ddobjectlink)) return '';

    $data = array();
    $data['subitems'] = array();
    foreach($ddobjectlink as $index => $subobjectlink) {
        $subobjectid = $subobjectlink['objectid'];

        // get some object information for this subobject (no need for a DD object here)
        $data['subitems'][$subobjectid] = xarModAPIFunc('dynamicdata','user','getobjectinfo',
                                                        array('objectid' => $subobjectid));
    }

    return xarTplModule('subitems','user','hook_item_new',$data);
}

?>

<?php
/**
 * Subitems module
 *
 * @package modules
 * @copyright (C) 2002-2009 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Subitems Module
 * @link http://xaraya.com/index.php/release/9356.html
 * @author Subitems Module Development Team
 */
function subitems_admin_ddobjectlink_view($args)
{
    if (!xarSecurityCheck('AdminSubitems')) return;

    $items = xarModAPIFunc('subitems','user','ddobjectlink_getall');
    if (!isset($items) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; // throw back

    for($i = 0; $i < count($items); $i++)
    {

        if(xarModIsAvailable($items[$i]['module']))
        {
            $items[$i]['modinfo'] = xarModGetInfo(xarModGetIdFromName($items[$i]['module']));
            $itemTypes = xarModAPIFunc($items[$i]['module'],'user','getitemtypes');
            $items[$i]['itemtypelabel'] = $itemTypes[$items[$i]['itemtype']]['label'];
        }
        $objectinfo = xarModAPIFunc('dynamicdata','user','getobjectinfo',
                                    array('objectid' => $items[$i]['objectid']));
        if (!empty($objectinfo))
            $items[$i]['label'] = $objectinfo['label'];
    }

    $data['ddobjects'] = $items;
    return $data;
}

?>

<?php
/**
 * Display a Dynamic Data item
 *
 * @package modules
 * @copyright (C) 2005-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Maxercalls Module
 * @link http://xaraya.com/index.php/release/247.html
 * @author Maxercalls module development team
 */
/**
 * Display a Dynamic Data item
 * This is a standard function to provide detailed informtion on a single item
 * available from the module.
 *
 * @param $args an array of arguments (if called by other modules)
 * @param int $args['objectid'] a generic object id (if called by other modules)
 * @param int $args['itemid'] the item id used for this module
 * @param int $args['itemtype'] the item type id used for this module
 * @return array
 */
function maxercalls_admin_display($args)
{
    if (!xarVarFetch('itemid',   'id', $itemid,    NULL, XARVAR_NOT_REQUIRED)) {return;}
    if (!xarVarFetch('itemtype', 'id', $itemtype,  NULL, XARVAR_NOT_REQUIRED)) {return;}
    if (!xarVarFetch('objectid', 'id', $objectid,  NULL, XARVAR_NOT_REQUIRED)) {return;}

    extract($args);

    if (!empty($objectid)) {
        $itemid = $objectid;
    }

    if (empty($itemid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    'item id', 'admin', 'display', 'maxercalls');
        xarErrorSet(XAR_USER_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return $msg;
    }

    $data['itemid'] = $itemid;
    $data['itemtype'] = $itemtype;

    if (!xarSecurityCheck('ReadMaxercalls',1,'Item',$itemid)) return;

    // get user settings for 'bold'
    $data['is_bold'] = xarModGetUserVar('maxercalls', 'bold');

    xarVarSetCached('Blocks.Maxercalls', 'itemid', $itemid);
    //$data['menu'] = xarModFunc('maxercalls','admin','menu');

    $item = array();
    $item['module'] = 'maxercalls';
    $item['returnurl'] = xarModURL('maxercalls', 'admin', 'display',
                                   array('itemid' => $itemid));
    $hooks = xarModCallHooks('item', 'display', $itemid, $item);
    if (empty($hooks)) {
        $data['hookoutput'] = '';
    } elseif (is_array($hooks)) {
        $data['hookoutput'] = join('',$hooks);
    } else {
        $data['hookoutput'] = $hooks;
    }

    // Return the template variables defined in this function
    return $data;
}
?>

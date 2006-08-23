<?php
/**
 * Security - Provides unix style privileges to xaraya items.
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Security Module
 * @author Brian McGilligan <brian@mcgilligan.us>
 */
/**
 * Display the security levels of an item.
 *
 * @param array $args
 */
function security_admin_display($args)
{
    if( !Security::check(SECURITY_ADMIN, 'security') ){ return false; }

    if( !xarVarFetch('modid',    'id',  $modid,    0,         XARVAR_NOT_REQUIRED) ){ return false; }
    if( !xarVarFetch('itemtype', 'id',  $itemtype, 0,         XARVAR_NOT_REQUIRED) ){ return false; }
    if( !xarVarFetch('itemid',   'id',  $itemid,   0,         XARVAR_NOT_REQUIRED) ){ return false; }
    extract($args);

    $data = array();

    $module = xarModGetInfo($modid);
    $items = xarModAPIFunc($module['name'], 'user', 'getitemlinks',
        array(
            'itemtype' => $itemtype,
            'itemids'  => array($itemid)
        )
    );
    $data['item'] = isset($items[$itemid]) ? $items[$itemid] : array('label' => xarML('Unknown'), 'url' => '');
    $data['security'] = xarModAPIFunc('security', 'user', 'get',
        array(
            'modid'    => $modid,
            'itemtype' => $itemtype,
            'itemid'   => $itemid
        )
    );

    $data['levels'] = xarModAPIFunc('security', 'user', 'getlevels');

    $data['modid']    = $modid;
    $data['itemtype'] = $itemtype;
    $data['itemid']   = $itemid;

    return xarTplModule('security', 'admin', 'display', $data);
}
?>
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
 * Modify th thru the security levels on items.
 *
 * @param array $args
 */
function security_admin_modify($args)
{
    if( !xarVarFetch('modid',    'id',  $modid,    0,         XARVAR_NOT_REQUIRED) ){ return false; }
    if( !xarVarFetch('itemtype', 'id',  $itemtype, 0,         XARVAR_NOT_REQUIRED) ){ return false; }
    if( !xarVarFetch('itemid',   'id',  $itemid,   0,         XARVAR_NOT_REQUIRED) ){ return false; }
    if( !xarVarFetch('submit',   'str', $submit,   null,      XARVAR_NOT_REQUIRED) ){ return false; }
    extract($args);

    xarModAPILoad('security');
    $has_admin_security = xarModAPIFunc('security', 'user', 'check',
        array(
            'modid'    => $modid,
            'itemtype' => $itemtype,
            'itemid'   => $itemid,
            'level'    => SECURITY_ADMIN,
            'hide_exception' => true
        )
    );
    if( !$has_admin_security ){ return ''; }

    if( !is_null($submit) )
    {
        if( !xarVarFetch('overview', 'array', $overview,array(), XARVAR_NOT_REQUIRED) ){ return false; }
        if( !xarVarFetch('read',     'array', $read,    array(), XARVAR_NOT_REQUIRED) ){ return false; }
        if( !xarVarFetch('comment',  'array', $comment, array(), XARVAR_NOT_REQUIRED) ){ return false; }
        if( !xarVarFetch('write',    'array', $write,   array(), XARVAR_NOT_REQUIRED) ){ return false; }
        if( !xarVarFetch('manage',   'array', $manage,  array(), XARVAR_NOT_REQUIRED) ){ return false; }
        if( !xarVarFetch('admin',    'array', $admin,   array(), XARVAR_NOT_REQUIRED) ){ return false; }

        $levels = array();
        $secLevels = xarModAPIFunc('security', 'user', 'getlevels');
        // Read checks from form and setup the levels for storage
        foreach( $secLevels as $secLevel )
        {
            foreach( $$secLevel['name'] as $role_id => $value )
            {
                $levels[$role_id][$secLevel['name']] = $value;
            }
        }

        $settings['levels'] = $levels;
        $sargs = array(
            'modid'    => $modid,
            'itemtype' => $itemtype,
            'itemid'   => $itemid,
            'settings' => $settings
        );
        xarModAPIFunc('security', 'admin', 'update', $sargs);

    }

    $data = array();

    $module = xarModGetInfo($modid);
    $items = xarModAPIFunc($module['name'], 'user', 'getitemlinks',
        array(
            'itemtype' => $itemtype,
            'itemids'  => array($itemid)
        )
    );
    $data['item'] = $items[$itemid];

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

    return xarTplModule('security', 'admin', 'modify', $data);
}
?>
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
    Adds a group security level to an item

    @return boolean true on success otherwise false
*/
function security_admin_creategroupsecurity($args)
{
    if( !xarVarFetch('modid',    'id', $modid,     0,  XARVAR_NOT_REQUIRED) ){ return false; }
    if( !xarVarFetch('itemtype', 'id', $itemtype,  0,  XARVAR_NOT_REQUIRED) ){ return false; }
    if( !xarVarFetch('itemid',   'id', $itemid,    0,  XARVAR_NOT_REQUIRED) ){ return false; }
    if( !xarVarFetch('group',    'id', $role_id,   0,  XARVAR_NOT_REQUIRED) ){ return false; }
    if( !xarVarFetch('returnurl','str',$returnUrl, '', XARVAR_NOT_REQUIRED) ){ return false; }

    extract($args);

    if( !xarModAPILoad('security', 'user') ){ return false; }

    if( !Security::check(SECURITY_ADMIN, 'security') ){ return false; }

    xarModAPIFunc('security', 'admin', 'create_group_level',
        array(
            'modid'    => $modid,
            'itemtype' => $itemtype,
            'itemid'   => $itemid,
            'role_id'  => $role_id,
            'levels'   => array(
                'overview' => 1,
                'read' => 1
            )
        )
    );

    xarResponseRedirect($returnUrl);

    return true;
}
?>
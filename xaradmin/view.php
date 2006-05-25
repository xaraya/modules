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
 * Browse thru the security levels on items.
 *
 * @param array $args
 */
function security_admin_view($args)
{
    if( !xarSecurityCheck('AdminSecurity') ){ return false; }

    if( !xarVarFetch('modid',    'id',  $modid,    0,         XARVAR_NOT_REQUIRED) ){ return false; }
    if( !xarVarFetch('itemtype', 'id',  $itemtype, 0,         XARVAR_NOT_REQUIRED) ){ return false; }
    if( !xarVarFetch('mode',     'str', $mode,     'modules', XARVAR_NOT_REQUIRED) ){ return false; }
    extract($args);

    $data = array();

    $data['items'] = xarModAPIFunc('security', 'user', 'getall',
        array(
            'modid'    => $modid,
            'itemtype' => $itemtype,
            'mode'     => $mode
        )
    );

//    var_dump($data['items']);

    $data['modid'] = $modid;
    $data['itemtype'] = $itemtype;

    return xarTplModule('security', 'admin', 'view', $data, $mode);
}
?>
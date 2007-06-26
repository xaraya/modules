<?php
/**
 * View users
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Members module
 */
/**
 * @author  Marc Lutolf <marcinmilan@xaraya.com>
 * view users
 */

function members_user_view($args)
{
    if (!xarSecurityCheck('ReadMembers')) return;
    if(!xarVarFetch('filetype', 'str:1', $filetype, '', XARVAR_NOT_REQUIRED)) {return;}
    if(!xarVarFetch('group', 'str:1', $group, '', XARVAR_NOT_REQUIRED)) {return;}
    if (empty($group)) {
		$role = xarRoles::get(xarModVars::get('members','defaultgroup'));
		$role->getName();
	}
    $return_url = xarServerGetCurrentURL();

    return array('defaultgroup' => $group, 'return_url'=>$return_url, 'filetype'=>$filetype);
}

?>

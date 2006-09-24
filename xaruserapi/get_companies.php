<?php
/**
 * Helpdesk Module
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Helpdesk Module
 * @link http://www.abraisontechnoloy.com/
 * @author Brian McGilligan <brianmcgilligan@gmail.com>
 */
/**
 *  Gets the valid companies of the current user if any exist
 *
 *  @param $args['keyvalue']
 */
function helpdesk_userapi_get_companies($args)
{
    extract($args);

    /*
        Detects if 'Companies' group exists.  If it does not then just return false.
        It's not an exception because this is just an optional concept for those that need it.
    */
    $roles = new xarRoles();
    if( !$roles->findRole('Companies') ){ return false; }

    $groups = xarModAPIFunc('roles', 'user', 'getallgroups',
        array(
            'parent' => 'Companies',
        )
    );

    $companies = array();
    if( !Security::check(SECURITY_MANAGE, 'helpdesk', 0, 0, false) )
    {
        // Lose all groups the user is not in
        $user = $roles->getRole( xarUserGetVar('uid') );
        $parents = $user->getParents(); // AND parents and groups

        foreach( $parents as $parent )
        {
            foreach( $groups as $group )
            {
                if( $parent->uid == $group['uid'] )
                {
                    $companies[$group['uid']] = $group;
                    break;
                }
            }
        }
    }
    else
    {
        foreach( $groups as $group )
        {
            $companies[$group['uid']] = $group;
        }
    }

    if( isset($keyvalue) && $keyvalue == true )
    {
        foreach($companies as $key => $value)
        {
            $companies[$key] = $value['name'];
        }
    }

    return $companies;
}
?>
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
    Gets the various security levels

    @param none

    @return array contains the security levels
*/
function security_userapi_getlevels($args)
{
    $levels = array();

    $levels[] = array(
        'name'  => 'overview',
        'label' => 'Overview',
        'level' => SECURITY_OVERVIEW
    );
    $levels[] = array(
        'name'  => 'read',
        'label' => 'Read',
        'level' => SECURITY_READ
    );
    $levels[] = array(
        'name'  => 'comment',
        'label' => 'Comment',
        'level' => SECURITY_COMMENT
    );
    $levels[] = array(
        'name'  => 'write',
        'label' => 'Write',
        'level' => SECURITY_WRITE
    );
    $levels[] = array(
        'name'  => 'manage',
        'label' => 'Manage',
        'level' => SECURITY_MANAGE
    );
    $levels[] = array(
        'name'  => 'admin',
        'label' => 'Admin',
        'level' => SECURITY_ADMIN
    );

    return $levels;
}
?>
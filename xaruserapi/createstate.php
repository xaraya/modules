<?php
/**
 * determine create user state
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Registration module
 * @link http://xaraya.com/index.php/release/30205.html
 */
/**
 * @access public
 * @author Jonathan Linowes
 * @since 1.23 - 2002/02/01
 * @param (none)
 * @return $state
 */
function registration_userapi_createstate($args)
{
    //extract($args);

    // where are we? define state
    $requireValidation = xarModGetVar('registration', 'requirevalidation');
    $pending = xarModGetVar('registration', 'explicitapproval');
    if ($requireValidation)     $state = ROLES_STATE_NOTVALIDATED;
    else if ($pending == 1)     $state = ROLES_STATE_PENDING;
    else                        $state = ROLES_STATE_ACTIVE;

    return $state;
}
?>
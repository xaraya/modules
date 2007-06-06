<?php
/**
 * create user
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
 * validate a user variable
 * @access public
 * @author Jonathan Linowes
 * @author Damien Bonvillain
 * @author Gregor J. Rothfuss
 * @since 1.23 - 2002/02/01
 * all params are required
 * @param 'username'
 * @param 'realname'
 * @param 'email'
 * @param 'pass'   password
 * @param 'state' one of ROLES_STATE_NOTVALIDATED, ROLES_STATE_PENDING, ROLES_STATE_ACTIVE
 * @return $id or null if failed
 */
function registration_userapi_createuser($args)
{
    extract($args);

    // setup params
    // need a password
    if (empty($pass)){
        $pass = xarModAPIFunc('roles', 'user', 'makepass');
    }
    // confirmation code
    $confcode = xarModAPIFunc('roles', 'user', 'makepass');
    // time registered
    $now = time();

    $userdata = array('uname'  => $username,
                    'realname' => $realname,
                    //FIXME...
                    'itemtype' => 2,
                    'email'    => $email,
                    'pass'     => $pass,
                    'date'     => $now,
                    'valcode'  => $confcode,
                    'state'    => $state);

    // Create user - this will also create the dynamic properties (if any) via the create hook
    $id = xarModAPIFunc('roles', 'admin', 'create', $userdata );

    // Check for user creation failure
    if ($id == 0) return;

    //Make sure the user email setting is off unless the user sets it
    xarModSetUserVar('roles','usersendemails', false, $id);

    /* Call hooks in here
     * This might be double as the roles hook will also call the create,
     * but the new hook wasn't called there, so no data is passed
     */
    $userdata['module'] = 'registration';
    $userdata['itemid'] = $id;
    xarModCallHooks('item', 'create', $id, $userdata);

    // Insert the user into the default users group
    $defaultgroup = xarModVars::get('roles', 'defaultgroup');
    if (empty($defaultgroup)) return;

    // Make the user a member of the users role
    if(!xarMakeRoleMemberByID($id, $defaultgroup)) return;

    // remember to congratulate this new user!
    if ($state != ROLES_STATE_NOTVALIDATED) {
        xarModVars::set('roles', 'lastuser', $id);
    }

    return $id;
}
?>
<?php
/**
 * Messages Module
 *
 * @package modules
 * @copyright (C) copyright-placeholder
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Messages Module
 * @link http://xaraya.com/index.php/release/6.html
 * @author XarayaGeek
 */
/**
 * Check to see if a user has any recipients they can send to
 * Pretty sure this function doesn't take any args
 * @return array|boolean $allowedsendmessages the IDs of groups this user can send to, or false if there are no groups the user can send to
 */

sys::import('modules.messages.xarincludes.defines');

/*Check if there are any possible recipients for the logged in user*/

function messages_userapi_isset_grouplist($args)
{
    extract($args);
    
    $users = xarMod::apiFunc(
        'roles',
        'user',
        'getall',
        array('state'   => 3,
                                    'include_anonymous' => false,
                                    'include_myself' => false)
    );
    $userid = xarUserGetVar('id');

    sys::import('xaraya.structures.query');

    $xartable = xarDB::getTables();
    $q = new Query('SELECT');
    $q->addtable($xartable['roles'], 'r');

    $q->eq('id', $userid);

    $q->addtable($xartable['rolemembers'], 'rm');
    $q->join('r.id', 'rm.role_id');

    if (!$q->run()) {
        return;
    }
    $CurrentUser =  $q->output();
    
    $id=$CurrentUser[0]['parent_id'];
    $groupID=$CurrentUser[0]['parent_id'];
    
    $allowedsendmessages = unserialize(xarModItemVars::get('messages', "allowedsendmessages", $groupID));
    
    if (isset($allowedsendmessages)) {
        if (empty($allowedsendmessages[0])) {
            return false;
        }
        $data['users'] = xarMod::apiFunc('messages', 'user', 'get_sendtousers');
        if (empty($data['users'])) {
            return false;
        }
        return $allowedsendmessages;
    } else {
        return false;
    }
}

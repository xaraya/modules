<?php
/**
 * Messages Module
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Messages Module
 * @link http://xaraya.com/index.php/release/6.html
 * @author XarayaGeek
 */
function messages_userapi_count_messages($args)
{
    extract($args);
    if (!isset($class)) $class ='total';
    
    switch ($class) {
        case 'total':
            $count = xarModAPIFunc('comments',
                                    'user',
                                    'get_count',
                                     array('modid'      => xarMod::getID('messages'),
                                           'objectid'   => xarSession::getVar('role_id'),
                                           'status'     => MESSAGES_STATUS_ACTIVE,
                                           'delete' => MESSAGES_DELETE_STATUS_VISIBLE_TO));
        break;

        case 'drafts':
        case 'outbox':
            $count = xarModAPIFunc('comments',
                                    'user',
                                    'get_author_count',
                                     array('modid'  => xarMod::getID('messages'),
                                           'status' => MESSAGES_STATUS_DRAFT,
                                           'author' => xarUserGetVar('id'),
                                           'delete' => MESSAGES_DELETE_STATUS_VISIBLE_FROM));
        break;

        case 'sent':
            $count = xarModAPIFunc('comments',
                                    'user',
                                    'get_author_count',
                                     array('modid'  => xarMod::getID('messages'),
                                           'status' => MESSAGES_STATUS_ACTIVE,
                                           'author' => xarSession::getVar('role_id'),
                                           'delete' => MESSAGES_DELETE_STATUS_VISIBLE_FROM));
        break;

        case 'unread':
            //Psspl:Modified the code for delete field.
            $count = xarModAPIFunc('comments',
                                    'user',
                                    'get_count',
                                     array('modid'      => xarMod::getID('messages'),
                                           'objectid'   => xarSession::getVar('role_id'),
                                           'status'     => MESSAGES_STATUS_ACTIVE,
                                           'delete'     => MESSAGES_DELETE_STATUS_VISIBLE_FROM));

            $read_messages = xarModUserVars::get('messages','read_messages');
            if (!empty($read_messages)) {
                $read_messages = unserialize($read_messages);
            } else {
                $read_messages = array();
            }

            $total_read = count($read_messages);

            /*
             * if total is zero or it's <= total_read,
             * then total unread equals zero
             */

            if (!$count || $count <= $total_read) {
                $count = 0;
            } else {
                $count -= $total_read;
            }
        break;
    }    
    return $count;
}
?>

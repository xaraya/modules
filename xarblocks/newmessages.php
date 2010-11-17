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
 *
 * @author Scot Gardner
 */
    sys::import('xaraya.structures.containers.blocks.basicblock');

    class Messages_NewmessagesBlock extends BasicBlock
    {
        public $name                = 'NewMessagesBlock';
        public $module              = 'messages';
        public $text_type           = 'Messages';
        public $text_type_long      = 'My Messages';
        public $allow_multiple      = true;

        function display(Array $data=array())
        {
            $data = parent::display($data);
            if (empty($data)) return;
            $vars = $data['content'];

            $itemtype=1;
        
            // Get Logged in Users ID
            $role_id = xarSession::getVar('role_id');
        
            // Count total Messages
            $totalin = xarMod::apiFunc('messages',
                                      'user',
                                      'get_count',
                                      array(
                                          'recipient' => $role_id
                        ));
            $vars['totalin'] = $totalin;
        
            // Count Unread Messages
            $unread = xarMod::apiFunc('messages',
                                      'user',
                                      'get_count',
                                      array(
                                          'recipient' => xarUserGetVar('id'),
                                          'unread'=>true
                        ));
            $vars['unread'] = $unread;
        
            // No messages return emptymessage
            if (empty($unread) || $unread == 0){ 
                $vars['content'] = 'No new messages';
                if (empty($data['title'])){
                    $data['title'] = xarML('My Messages');
                }
                $data['content'] = $vars;
            } else {
                $vars['numitems'] = $unread;
                $data['content'] = $vars;
        
                if (empty($data['title'])){
                    $data['title'] = xarML('My Messages');
                }
            }
            return $data;
        }
    }        
?>

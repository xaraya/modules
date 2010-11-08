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

            $data = array();
            $itemtype=1;
        
            // Get Logged in Users ID
            $role_id = xarSession::getVar('role_id');
        
            // Count total Messages
            $totalin = xarModAPIFunc('messages',
                                      'user',
                                      'get_count',
                                      array(
                                          'recipient' => $role_id
                        ));
            $data['totalin'] = $totalin;
        
            // Count Unread Messages
            $unread = xarModAPIFunc('messages',
                                      'user',
                                      'get_count',
                                      array(
                                          'recipient' => xarUserGetVar('id'),
                                          'unread'=>true
                        ));
            $data['unread'] = $unread;
        
            // No messages return emptymessage
            if (empty($unread) || $unread == 0){
                $data['emptymessage'] = xarML('No Unread messages in mailbox');
                $data['content'] = 'No new Messages';
                if (empty($data['title'])){
                    $data['title'] = xarML('My Messages');
                }
        
                $blockinfo['content'] = $data;
                return $blockinfo;
            } else {
                $data['emptymessage'] = '';
                $data['numitems'] = $numitems;
                $blockinfo['content'] = $data;
        
                if (empty($blockinfo['title'])){
                    $blockinfo['title'] = xarML('My Messages');
                }
            }
            return $blockinfo;
        }
    }        
?>

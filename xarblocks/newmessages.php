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
    // File Information, supplied by developer, never changes during a versions lifetime, required
    protected $type             = 'newmessages';
    protected $module           = 'messages'; // module block type belongs to, if any
    protected $text_type        = 'New Messages';  // Block type display name
    protected $text_type_long   = 'Show new messages for logged in users'; // Block type description
    // Additional info, supplied by developer, optional
    protected $type_category    = 'block'; // options [(block)|group]
    protected $author           = '';
    protected $contact          = '';
    protected $credits          = '';
    protected $license          = '';

    // blocks subsystem flags
    protected $show_preview = true;  // let the subsystem know if it's ok to show a preview
    // @todo: drop the show_help flag, and go back to checking if help method is declared
    protected $show_help    = false; // let the subsystem know if this block type has a help() method

        function display()
        {
            $vars = $this->getContent();

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
                return xarML('No new messages');
            } else {
                $vars['numitems'] = $unread;
            }
            return $vars;
        }
    }
?>

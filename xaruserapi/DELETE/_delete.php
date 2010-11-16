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
 * Delete a message
 *
 * @author Carl P. Corliss (aka rabbitt)
 * @access  public
 * @param   integer     $id   the id of the message to delete
 * @returns bool true on success, false otherwise
 */

sys::import('modules.messages.xarincludes.defines');

function messages_userapi_delete( $args )
{

    extract($args);

    if (!isset($folder)) throw new Exception(xarML('Missing folder for delete'));
    if (!isset($object)) throw new Exception(xarML('Missing object for delete'));

    if($folder == 'sent' || $folder == 'drafts'){
        $object->properties['author_delete']->setValue(MESSAGES_DELETED);
    } else {
        $object->properties['recipient_delete']->setValue(MESSAGES_DELETED);
    }

    $object->updateItem();
    return true;
}

?>

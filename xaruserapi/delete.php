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
/**
 * Delete a message
 *
 * @author Carl P. Corliss (aka rabbitt)
 * @access  public
 * @param   integer     $id   the id of the message to delete
 * @returns bool true on success, false otherwise
 */

function messages_userapi_delete( $args )
{

    extract($args);

    if (!isset($folder)) throw new Exception(xarML('Missing folder for delete'));
    if (!isset($object)) throw new Exception(xarML('Missing object for delete'));

    $deleted = $object->properties['deleted']->getValue();
    if($folder=='inbox'){
        if ($deleted == MESSAGES_DELETE_STATUS_ACTIVE) $deleted = MESSAGES_DELETE_STATUS_VISIBLE_FROM;
        elseif ($deleted == MESSAGES_DELETE_STATUS_VISIBLE_TO) $deleted = MESSAGES_DELETE_STATUS_DELETED;
    } else {
        if ($deleted == MESSAGES_DELETE_STATUS_ACTIVE) $deleted = MESSAGES_DELETE_STATUS_VISIBLE_TO;
        elseif ($deleted == MESSAGES_DELETE_STATUS_VISIBLE_FROM) $deleted = MESSAGES_DELETE_STATUS_DELETED;
    }
    $object->properties['deleted']->setValue($deleted);
    $object->updateItem();
    return true;
}

?>
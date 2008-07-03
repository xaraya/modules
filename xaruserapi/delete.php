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

    if (!isset($id)) {
        $msg = xarML('Missing #(1) for #(2) function #(3)() in module #(4)',
                     'id', 'userapi', 'delete', 'messages');
        throw new Exception($msg);
    }

    return (bool) xarModAPIFunc('comments',
                                'admin',
                                'delete_branch',
                                 array('node' => $id));

}

?>

<?php

/**
 * Delete a message
 *
 * @author Carl P. Corliss (aka rabbitt)
 * @access  public
 * @param   integer     $mid   the id of the message to delete
 * @returns bool true on success, false otherwise
 */

function messages_userapi_delete( $args ) 
{

    extract($args);

    if (!isset($mid)) {
        $msg = xarML('Missing #(1) for #(2) function #(3)() in module #(4)',
                     'mid', 'userapi', 'delete', 'messages');
        xarExceptionSet(XAR_USER_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return;
    }

    return (bool) xarModAPIFunc('comments',
                                'admin',
                                'delete_branch',
                                 array('node' => $mid));

}

?>

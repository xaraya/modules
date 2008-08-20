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
function messages_userapi_count_total()
{
    //Psspl:Modifided the code for delete_to field.
    $total = xarModAPIFunc('comments',
                            'user',
                            'get_count',
                             array('modid'      => xarModGetIDFromName('messages'),
                                   'objectid'   => xarSession::getVar('role_id'),
                                   'status'     => 2,
                                   'delete_to' => 1));

    return $total;
}
?>

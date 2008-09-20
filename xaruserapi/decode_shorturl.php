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
function messages_userapi_decode_shorturl( $params )
{


    if ( $params[0] != 'messages' )
        return;

    if (empty($params[1]))
        $params[1] = '';

    switch ($params[1]) {
        case 'Outbox':
            return array('send', array());
            break;
        case 'Trash':
            return array('delete', array());
            break;
        default:
        case 'Inbox':
            if (isset($params[2])) {
                return array('display', array('id' => $params[2]));
            } else {
                return array('view', array());
            }
            break;
    }

}
?>

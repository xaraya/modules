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

sys::import('modules.messages.xarincludes.defines');

function messages_userapi_decode_shorturl($params) {

    if ($params[0] != 'messages')
        return;

    if (empty($params[1]))
        $params[1] = '';

    switch ($params[1]) {
        case 'new':
            return array('new', array());
            break;
		case 'modify':
            return array('modify', array('id' => $params[2]));
            break;
		case 'reply':
            return array('reply', array('id' => $params[2]));
            break;
		case 'sent':
            return array('view', array('folder' => 'sent'));
            break;
        case 'drafts':
            return array('view', array('folder' => 'drafts'));
            break;
		case 'delete':
            return array('delete', array());
            break;
        default:
        case 'inbox':
            if (isset($params[2])) {
                return array('display', array('id' => $params[2]));
            } else {
                return array('view', array());
            }
            break;
    }

}
?>

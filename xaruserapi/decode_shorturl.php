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

    if (empty($params[1])) $params[1] = '';

	if (is_numeric($params[1])) {
		return array('display', array('id' => $params[1]));
	}

    switch ($params[1]) {
        case 'new': 
			$args = array();
			if (isset($params[2])) {
				$args['to'] = $params[2];
			}
			if (isset($params[3]) && $params[3] == 'opt') {
				$args['opt'] = true;
			}
            return array('new', $args);
            break;
		case 'modify':
            return array('modify', array('id' => $params[2]));
            break;
		case 'reply':
            return array('reply', array('replyto' => $params[2]));
            break;
		case 'markunread':
            return array('markunread', array('id' => $params[2]));
            break;
		case 'sent':
            return array('view', array('folder' => 'sent'));
            break;
        case 'drafts':
            return array('view', array('folder' => 'drafts'));
            break;
		case 'delete':
            return array('delete', array('id' => $params[2]));
            break;
        default:
        case 'inbox':
            return array('view', array());
            break;
    }

}
?>

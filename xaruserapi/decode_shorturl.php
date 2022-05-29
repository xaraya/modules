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

function messages_userapi_decode_shorturl($params)
{
    if ($params[0] != 'messages') {
        return;
    }

    if (empty($params[1])) {
        $params[1] = '';
    }

    if (is_numeric($params[1])) {
        return ['display', ['id' => $params[1]]];
    }

    switch ($params[1]) {
        case 'new':
            $args = [];
            if (isset($params[2])) {
                $args['to_id'] = $params[2];
            }
            if (isset($params[3]) && $params[3] == 'opt') {
                $args['opt'] = true;
            }
            return ['new', $args];
            break;
        case 'modify':
            return ['modify', ['id' => $params[2]]];
            break;
        case 'reply':
            return ['reply', ['replyto' => $params[2]]];
            break;
        case 'markunread':
            return ['markunread', ['id' => $params[2]]];
            break;
        case 'sent':
            return ['view', ['folder' => 'sent']];
            break;
        case 'drafts':
            return ['view', ['folder' => 'drafts']];
            break;
        case 'delete':
            return ['delete', ['id' => $params[2]]];
            break;
        default:
        case 'inbox':
            return ['view', []];
            break;
    }
}

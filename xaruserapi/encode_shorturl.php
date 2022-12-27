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

function messages_userapi_encode_shorturl($args)
{
    $func = null;
    $module = null;
    $id = null;
    $rest = [];

    foreach ($args as $name => $value) {
        switch ($name) {
            case 'module':
                $module = $value;
                break;
            case 'id':
                $id = $value;
                break;
            case 'replyto':
                $replyto = $value;
                break;
            case 'func':
                $func = $value;
                break;
            case 'to_id':
                $to_id = $value;
                break;
            case 'folder':
                $folder = $value;
                break;
            default:
                $rest[$name] = $value;
        }
    }

    // kind of a assertion :-))
    if (isset($module) && $module != 'messages') {
        return;
    }

    /*
     * LETS GO. We start with the module.
     */
    $path = '/messages';

    if (empty($func)) {
        return;
    }

    switch ($func) {
        case 'delete':
            $path .= '/delete';
            if (isset($id)) {
                $path .= '/' . $id;
                unset($id);
            }
            break;
        case 'markunread':
            $path .= '/markunread';
            if (isset($id)) {
                $path .= '/' . $id;
                unset($id);
            }
            break;
        case 'new':
            $path .= '/new';
            if (isset($to_id)) {
                $path .= '/' . $to_id;
                unset($to_id);
            }
            if (isset($opt) && $opt) {
                $path .= '/opt';
            }
            break;
        case 'modify':
            $path .= '/modify';
            if (isset($id)) {
                $path .= '/' . $id;
                unset($id);
            }
            break;
        case 'reply':
            $path .= '/reply';
            if (isset($replyto)) {
                $path .= '/' . $replyto;
            }
            break;
        case 'display':
            $path .= '/' . $id;
            break;
        case 'main':
        default: // main, view
            if (isset($folder)) {
                if ($folder == 'sent') {
                    $path .= '/sent';
                } elseif ($folder == 'drafts') {
                    $path .= '/drafts';
                }
            } else {
                $path .= '/inbox'; // default
            }
            break;
    }

    if (isset($id) && $func != 'display' && $func != 'reply' && $func != 'delete') {
        $rest['id'] = $id;
    }

    if (isset($replyto)) {
        $rest['replyto'] = $replyto;
    }

    if (($func = 'markunread' || $func == 'display') && isset($folder)) {
        $rest['folder'] = $folder;
    }

    $add = [];
    foreach ($rest as $key => $value) {
        if (isset($rest[$key])) {
            $add[] =  $key . '=' . $value;
        }
    }

    if (count($add) > 0) {
        $path = $path . '?' . implode('&', $add);
    }

    return $path;
}

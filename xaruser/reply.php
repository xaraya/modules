<?php
/**
 * Messages Module
 *
 * @package modules
 * @subpackage messages module
 * @copyright (C) copyright-placeholder
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://xaraya.com/index.php/release/6.html
 * @author XarayaGeek
 * @author Ryan Walker
 * @author Marc Lutolf <mfl@netspan.ch>
 */

sys::import('modules.messages.xarincludes.defines');

function messages_user_reply()
{
    if (!xarSecurity::check('AddMessages')) {
        return;
    }

    if (!xarVar::fetch('object', 'str', $object, 'messages_messages', xarVar::NOT_REQUIRED)) {
        return;
    }
    if (!xarVar::fetch('replyto', 'int', $replyto, 0, xarVar::NOT_REQUIRED)) {
        return;
    }
    xarResponse::redirect(xarController::URL('messages', 'user', 'new', ['replyto' => $replyto]));
    return true;
}

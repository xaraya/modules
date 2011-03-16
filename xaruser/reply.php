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

function messages_user_reply() {
 
	if (!xarSecurityCheck('AddMessages')) return;

	if (!xarVarFetch('object', 'str', $object, 'messages_messages', XARVAR_NOT_REQUIRED)) return;
	if (!xarVarFetch('replyto', 'int', $replyto, 0, XARVAR_NOT_REQUIRED)) return; 
	xarResponse::redirect(xarModURL('messages','user','new',array('replyto' => $replyto)));
	return true;
}

?>

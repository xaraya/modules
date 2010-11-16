<?php

sys::import('modules.messages.xarincludes.defines');

function messages_user_reply() {

	if (!xarSecurityCheck('AddMessages')) return;

	if (!xarVarFetch('object', 'str', $object, 'messages_messages', XARVAR_NOT_REQUIRED)) return;
	if (!xarVarFetch('replyto', 'int', $replyto, 0, XARVAR_NOT_REQUIRED)) return; 
	xarResponse::redirect(xarModURL('messages','user','new',array('replyto' => $replyto)));
	return true;
}

?>

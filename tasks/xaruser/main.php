<?php
/**
 * Task list view
 *
 */
function tasks_user_main()
{
	xarResponseRedirect(xarModURL('tasks','user','view'));
	return true;
}

?>
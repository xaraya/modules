<?php
/**
 * View tasklist
 *
 */
function tasks_admin_view()
{
    xarResponseRedirect(xarModURL('tasks','user','view'));
	return true;
}

?>
<?php
/**
 * Update configuration
 *
 */

function tasks_admin_updateconfig()
{
    list($dateformat,
		$showoptions,
		$returnfromadd,
		$returnfromedit,
		$returnfromsurface,
		$returnfrommigrate,
		$maxdisplaydepth) = xarVarCleanFromInput('dateformat',
												'showoptions',
												'returnfromadd',
												'returnfromedit',
												'returnfromsurface',
												'returnfrommigrate',
												'maxdisplaydepth');

    xarModSetVar('tasks', 'dateformat', $dateformat);
    xarModSetVar('tasks', 'showoptions', $showoptions);
    xarModSetVar('tasks', 'returnfromadd', $returnfromadd);
    xarModSetVar('tasks', 'returnfromedit', $returnfromedit);
    xarModSetVar('tasks', 'returnfromsurface', $returnfromsurface);
    xarModSetVar('tasks', 'returnfrommigrate', $returnfrommigrate);
    xarModSetVar('tasks', 'maxdisplaydepth', $maxdisplaydepth);

    xarResponseRedirect(xarModURL('tasks', 'admin', 'modifyconfig'));

    return true;
}

?>
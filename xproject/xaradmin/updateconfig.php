<?php

function xproject_admin_updateconfig()
{
    if (!xarSecConfirmAuthKey()) return;

	list($displaydates,
		$displayhours,
		$displayfrequency,
		$accessrestricted,
		$dateformat,
		$maxdone,
		$mostimportantdays,
		$refreshmain,
		$sendmails,
		$showextraasterisk,
		$showlinenumbers,
		$showpercent,
		$showpriority,
		$todoheading,
		$veryimportantdays,
		$itemsperpage) = xarVarCleanFromInput('displaydates',
												'displayhours',
												'displayfrequency',
												'accessrestricted',
												'dateformat',
												'maxdone',
												'mostimportantdays',
												'refreshmain',
												'sendmails',
												'showextraasterisk',
												'showlinenumbers',
												'showpercent',
												'showpriority',
												'todoheading',
												'veryimportantdays',
												'itemsperpage');
/*												
	trim($newprojectname);
	if(!empty($newprojectname)) {
		xarModAPIFunc('categories',
						  'admin', 
						  'create', 
						  Array('name' => $newprojectname,
								'description' => $newprojectdesc,
								'parent_id' => xarModGetVar('xproject', 'projectmastercid')));
	}
*/
    if (!isset($displaydates)) $displaydates = false;
    if (!isset($displayhours)) $displayhours = false;
    if (!isset($displayfrequency)) $displayfrequency = false;
    if (!isset($accessrestricted)) $accessrestricted = false;
    if (!isset($dateformat)) $dateformat = 1;
    if (!isset($maxdone)) $maxdone = 10;
    if (!isset($mostimportantdays)) $mostimportantdays = 0;
    if (!isset($refreshmain)) $refreshmain = 600;
    if (!isset($sendmails)) $sendmails = false;
    if (!isset($showextraasterisk)) $showextraasterisk = false;
    if (!isset($showlinenumbers)) $showlinenumbers = false;
    if (!isset($showpercent)) $showpercent = false;
    if (!isset($showpriority)) $showpriority = false;
    if (!isset($todoheading)) $todoheading = "Task Management Administration";
    if (!isset($veryimportantdays)) $veryimportantdays = 0;
    if (!isset($itemsperpage)) $itemsperpage = 20;

    xarModSetVar('xproject', 'displaydates', $displaydates);
    xarModSetVar('xproject', 'displayhours', $displayhours);
    xarModSetVar('xproject', 'displayfrequency', $displayfrequency);
    xarModSetVar('xproject', 'accessrestricted', $accessrestricted);
    xarModSetVar('xproject', 'dateformat', $dateformat);
    xarModSetVar('xproject', 'maxdone', $maxdone);
    xarModSetVar('xproject', 'mostimportantdays', $mostimportantdays);
    xarModSetVar('xproject', 'refreshmain', $refreshmain);
    xarModSetVar('xproject', 'sendmails', $sendmails);
    xarModSetVar('xproject', 'showextraasterisk', $showextraasterisk);
    xarModSetVar('xproject', 'showlinenumbers', $showlinenumbers);
    xarModSetVar('xproject', 'showpercent', $showpercent);
    xarModSetVar('xproject', 'showpriority', $showpriority);
    xarModSetVar('xproject', 'todoheading', $todoheading);
    xarModSetVar('xproject', 'veryimportantdays', $veryimportantdays);
    xarModSetVar('xproject', 'itemsperpage', $itemsperpage);
	
    xarModCallHooks('module','updateconfig','xproject',
                   array('module' => 'xproject'));
				   
    xarResponseRedirect(xarModURL('xproject', 'admin', 'main'));

    return true;
}

?>
<?PHP
// ----------------------------------------------------------------------
// eNvolution Content Management System
// Copyright (C) 2002 by the eNvolution Development Team.
// http://www.envolution.com/
// ----------------------------------------------------------------------
// Based on:
// Postnuke Content Management System - www.postnuke.com
// PHP-NUKE Web Portal System - http://phpnuke.org/
// Thatware - http://thatware.org/
// ----------------------------------------------------------------------
// LICENSE
//
// This program is free software; you can redistribute it and/or
// modify it under the terms of the GNU General Public License (GPL)
// as published by the Free Software Foundation; either version 2
// of the License, or (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// To read the license please visit http://www.gnu.org/copyleft/gpl.html
//
/**
 * initialise block
 */
function dq_helpdesk_dq_helpdeskblock_init()
{
    // Security
    pnSecAddSchema('dq_helpdesk:blocks:', 'Block title::');
}

/**
 * get information on block
 */
function dq_helpdesk_dq_helpdeskblock_info()
{
    // Values
    return array('text_type' => 'DQ Help Desk',
                 'module' => 'dq_helpdesk',
                 'text_type_long' => 'Show Most recent Tickets submitted',
                 'allow_multiple' => true,
                 'form_content' => false,
                 'form_refresh' => false,
                 'show_preview' => true);
}

/**
 * display block
 */
function dq_helpdesk_dq_helpdeskblock_display($blockinfo)
{
    // Security check
    if (!pnSecAuthAction(0,
                         'dq_helpdesk:blocks:',
                         "$blockinfo[title]::",
                         ACCESS_READ)) {
        return;
    }

    // Get variables from content block
    $vars = pnBlockVarsFromContent($blockinfo['content']);

    // Defaults
    if (empty($vars['numtickets'])) {
        $vars['numtickets'] = 5;
    }
	// Load the user api for the module, database interaction for 
	// this block has been placed there.
    // Primary function for getting a list of tickets:
	// dq_helpdesk_userapi_gettickets
	// Possible ARGS: 
	// selection - UNASSIGNED, MYALL, MYOPEN, MYCLOSED
	//             MYASSIGNEDALL, MYASSIGNEDOPEN, MYASSIGNEDCLOSED
	//             ALL, OPEN, CLOSED
	// sortorder - TICKET_ID_DESC, TICKET_ID_ASC, DATEUPDATED_DESC, DATEUPDATED_ASC
	//
	// lastxtickets - Integer value greater than zero (0)
	//
	// Returned ARRAY:
	//			'ticket_id' 	=> $ticket_id, 
	//			'ticketdate' 	=> dq_helpdesk_userapi_formatdate($ticketdate),
	//			'subject' 		=> $subject,
	//			'status' 		=> dq_helpdesk_userapi_getstatusidname($statusid),
	//			'priority'		=> dq_helpdesk_userapi_getpriorityidname($priorityid),
	//			'prioritycolor'	=> dq_helpdesk_userapi_getpriorityidcolor($priorityid),
	//			'pricolor'		=> dq_helpdesk_userapi_getpriorityidcolor($priorityid),
	//			'lastupdate'	=> dq_helpdesk_userapi_formatdate($lastupdate),
	//			'assignedto'	=> $assignedto
	//			'openedby'		=> $openedby
	if (!pnModAPILoad('dq_helpdesk', 'user')) {
        $output->Text(_LOADFAILED);
        return $output->GetOutput();
		break;
    }
	
	$selection = "OPEN";
	$sortorder = "TICKET_ID_DESC";
	$lastxtickets = $vars['numtickets'];
	// Language Info
	$currentlang 	= pnUserGetLang();
	// Module Info
	$modid      	= pnModGetIDFromName('dq_helpdesk');
	$modinfo    	= pnModGetInfo($modid);
	$enabledimages 	= pnModGetVar('dq_helpdesk', 'Enable Images');
	include_once 'modules/'.$modinfo['directory'].'/pnincludes/tools.php';
	$AllowUserCheckStatus = pnModGetVar('dq_helpdesk', 'User can check status');
	$AllowUserSubmitTicket = pnModGetVar('dq_helpdesk', 'User can Submit');
	$AllowAnonSubmitTicket = pnModGetVar('dq_helpdesk', 'Anonymous can Submit');
	$enforceauthkey=pnModGetVar('dq_helpdesk', 'EnforceAuthKey');
	// User Info
	$EditAccess = pnSecAuthAction(0, 'dq_helpdesk::', '::', ACCESS_EDIT);
	$UserLoggedIn = pnUserLoggedIn();
	$userid = pnUserGetVar('uid');
	$mytickets_data = pnModAPIFunc('dq_helpdesk','user','gettickets',array('userid'=>$userid,'selection'=>$selection,'sortorder'=>$sortorder,'lastxtickets'=>$lastxtickets));

	$output = new pnHTML();
	$output->SetInputMode(_PNH_VERBATIMINPUT);
	$output->Text("<div align='center'>"._LAST." ".$lastxtickets." "._TICKETS."<br><hr width='90%'></div>");
	
	foreach($mytickets_data as $ticket) {
		$output->Text('<form action="'.pnModURL('dq_helpdesk', 'user', 'viewticket').'" method="post" name="VIEWBLOCKTICKET'.$ticket['ticket_id'].'">');
		$output->Text("<a href='javascript:document.VIEWBLOCKTICKET".$ticket['ticket_id'].".submit();' title='"._VIEWTICKET." ".$ticket['ticket_id']."'>".$ticket['ticketdate']."</a> ");
		$output->Text(pnModAPIFunc('dq_helpdesk','user','getusernamelink',$ticket['openedby']));
		$output->LineBreak(1);
		if ($enforceauthkey){
				$output->FormHidden('authid', pnSecGenAuthKey());
			}
		$output->FormHidden('userid', $userid);
		$output->FormHidden('activity', 'VIEWTICKET');
		$output->FormHidden('ticket_id', $ticket['ticket_id']);
		$output->Text("<a href='javascript:document.VIEWBLOCKTICKET".$ticket['ticket_id'].".submit();' title='"._VIEWBLOCKTICKET." ".$ticket['ticket_id']."'><strong><big>&middot;</big></strong> ".$ticket['subject']."</a>&nbsp;");
		$output->FormEnd();
	}
	$output->Text("<div align='center'>");
	if (($UserLoggedIn && $AllowUserCheckStatus) || ($AllowAnonSubmitTicket) ||($EditAccess)) {
		if ($enabledimages)
		{
			$output->URL(pnModURL('dq_helpdesk', 'user', 'newticket'), '<img src="'.get_icon("newticket").'" border="0">'); 	
		}else{
			$output->Text("[");
			$output->URL(pnModURL('dq_helpdesk', 'user', 'newticket'), _NEWTICKET); 
			$output->Text("]");
		}
	}
	if (($UserLoggedIn && $AllowUserCheckStatus) || $EditAccess) {
			if ($enabledimages)
			{
				$output->URL(pnModURL('dq_helpdesk', 'user', 'searchtickets'), '<img src="'.get_icon("search.gif").'" border=0>'); 
			}else{
				$output->Text(" [");
				$output->URL(pnModURL('dq_helpdesk', 'user', 'searchtickets'), _SEARCHTICKETS); 
				$output->Text("]");
			}
	}
	$output->Text("</div>");
    // Populate block info and pass to theme
    $blockinfo['content'] = $output->GetOutput();
    return themesideblock($blockinfo);
}


/**
 * modify block settings
 */
function dq_helpdesk_dq_helpdeskblock_modify($blockinfo)
{
    // Create output object
    $output = new pnHTML();

    // Get current content
    $vars = pnBlockVarsFromContent($blockinfo['content']);

    // Defaults
    if (empty($vars['numtickets'])) {
        $vars['numtickets'] = 5;
    }

    // Create row
    $row = array();
    $output->SetOutputMode(_PNH_RETURNOUTPUT);
    $row[] = $output->Text(_NUMTICKETS);
    $row[] = $output->FormText('numtickets',
                               pnVarPrepForDisplay($vars['numtickets']),
                               5,
                               5);
    $output->SetOutputMode(_PNH_KEEPOUTPUT);

    // Add row
    $output->SetInputMode(_PNH_VERBATIMINPUT);
    $output->TableAddRow($row, 'left');
    $output->SetInputMode(_PNH_PARSEINPUT);

    // Return output
    return $output->GetOutput();
}

/**
 * update block settings
 */
function dq_helpdesk_dq_helpdeskblock_update($blockinfo)
{
    $vars['numtickets'] = pnVarCleanFromInput('numtickets');

    $blockinfo['content'] = pnBlockVarsToContent($vars);

    return $blockinfo;
}

?>


<?php
/********************************************************/
/* Dimensionquest Help Desk                             */
/*  Development by:                                     */
/*     Burke Azbill - burke@dimensionquest.net          */
/*                                                      */
/* This program is opensource so you can do whatever    */
/* you want with it.                                    */
/*                                                      */
/* http://www.dimensionquest.net               		    */
/********************************************************/

function helpdesk_userapi_isticketowner($ticket_id)
{
    //extract($args);
    //list($ticket_id,$userid)=xarVarCleanFromInput('ticket_id','userid');
    list($dbconn) = xarDBGetConn();
    $xartable = xarDBGetTables();
    $db_table = $xartable['helpdesk_tickets'];
    $db_column = &$xartable['helpdesk_tickets_column'];

    $sql = "SELECT $db_column[ticket_id],
                   $db_column[ticket_openedby]
            FROM  $db_table
            WHERE $db_column[ticket_id]=".$ticket_id;
    $results = $dbconn->Execute($sql);
    
    // Now compare the results to see if the person making the request
    // is the same as the id entered in the "openedby" field
    if ($results->fields[1] == xarUserGetVar('uid')){
        return 1;
    }else{
        return 0;
    }
}

function helpdesk_new_id($args)
{
    extract($args);
    if (!isset($table) || !isset($field)) {
        xarSessionSetVar('errormsg', _MODARGSERROR);
        return false;
    }
    list($dbconn) = xarDBGetConn();
    $xartable = xarDBGetTables();
    $db_table = $xartable['helpdesk_'.$table];
    $db_column = &$xartable['helpdesk_'.$table.'_column'];
    $sql = "Select max(".$db_column[$field].") from ".$db_table;
    $newID = $dbconn->Execute($sql);
    return (($newID->fields[0])+1);
}

//////////////////////////////////////     BLOCK FUNCTIONS     /////////////////////////////////////////
function helpdesk_userapi_getlastx($ticketcount)
{
    // Input: Integer - number of most recent tickets to request
    // Returns an Array of data to be parsed by block functions
    list($dbconn) = xarDBGetConn();
    $xartable = xarDBGetTables();
    $helpdesktable = $xartable['helpdesk_tickets'];
    $helpdeskcolumn = &$xartable['helpdesk_tickets_column'];
    if($ticketcount < 1)
    {
        return 0;
    }


}
?>

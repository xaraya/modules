<?php
/**
* Run Search 
*
* Run the search query against the DB
*
* @author  Brian McGilligan bmcgilligan@abrasiontechnology.com
* @access  public / private / protected
* @param   
* @param   
* @return  template
* @throws  list of exception identifiers which can be thrown
* @todo    <Brian McGilligan> ;  
*/ 
function helpdesk_userapi_runsearch($args)
{
    xarVarFetch('keywords',   'str:1:',  $keywords,   null,  XARVAR_NOT_REQUIRED);
    xarVarFetch('subject',    'int:1:',  $subject,    null,  XARVAR_NOT_REQUIRED);
    xarVarFetch('history',    'int:1:',  $history,    null,  XARVAR_NOT_REQUIRED);
    xarVarFetch('notes',      'int:1:',  $notes,      null,  XARVAR_NOT_REQUIRED);
    xarVarFetch('cboSearch1', 'str:1:',  $cboSearch1, null,  XARVAR_NOT_REQUIRED);
    xarVarFetch('cboSearch2', 'str:1:',  $cboSearch2, null,  XARVAR_NOT_REQUIRED);

    $wordArray = split(" ",$keywords);
    $wordCount = count($wordArray);

    // Database information
    list($dbconn)    = xarDBGetConn();
    $xartable        = xarDBGetTables();
    $helpdesktable   = $xartable['helpdesk_histories'];
    $helpdeskcolumn  = &$xartable['helpdesk_histories_column'];
    $helpdesktable2  = $xartable['helpdesk_tickets'];
    $helpdeskcolumn2 = &$xartable['helpdesk_tickets_column'];

    $sql = "SELECT DISTINCTROW $helpdeskcolumn[ticket_id],       $helpdeskcolumn[ticket_id], 
                               $helpdeskcolumn2[ticket_subject], $helpdeskcolumn2[ticket_lastupdate], 
                               $helpdeskcolumn2[ticket_date]
            FROM      $helpdesktable2 
            LEFT JOIN $helpdesktable ON ($helpdeskcolumn2[ticket_id] = $helpdeskcolumn[ticket_id])";
    // build WHERE statement
    $sql .= " WHERE (";
    $wherestr="";
    // 1st word comparison procedures
    if ($history){
        $wheresql .= "($helpdeskcolumn[history] LIKE '%".$wordArray[0]."%')";
        if ($notes){
            $wheresql .="OR ($helpdeskcolumn[history_notes] LIKE '%".$wordArray[0]."%')";
        }
        if ($subject){
            $wheresql .="OR ($helpdeskcolumn2[ticket_subject] LIKE '%".$wordArray[0]."%')";
        }	
    }elseif($notes){
        $wheresql .="($helpdeskcolumn[history_notes] LIKE '%".$wordArray[0]."%')";
        if ($subject){
            $wheresql .="OR ($helpdeskcolumn2[ticket_subject] LIKE '%".$wordArray[0]."%')";
        }
    }elseif($subject){
        // neither of the other search fields were indicated, so no need for "OR" at this point
        $wheresql .="($helpdeskcolumn2[ticket_subject] LIKE '%".$wordArray[0]."%')";
    }

    if ($wordCount > 1)
        {
        $i=1;
        while($i<$wordCount) {
            // These don't need the same special treatment that the first word did, ALL of these will
            // require an "OR" in front of the comparison operators
            if ($history){
                $wheresql .= " OR ($helpdeskcolumn[history] LIKE '%".$wordArray[$i]."%')";
            }
            if ($notes){
                $wheresql .=" OR ($helpdeskcolumn[history_notes] LIKE '%".$wordArray[$i]."%')";
            }
            if ($subject){
                $wheresql .=" OR ($helpdeskcolumn2[ticket_subject] LIKE '%".$wordArray[$i]."%')";
            }
            $i++;
        }
    }
    $sql .= $wheresql.")";
    // End Where statement

    // Build Sort condition:
    if($cboSearch1>0)
        {
        switch ($cboSearch1) {
            case "1":
                $sql .= " ORDER BY $helpdeskcolumn2[ticket_lastupdate]";
                break;
            case "3":
                $sql .= " ORDER BY $helpdeskcolumn2[ticket_date]";
                break;
            case "2":
                $sql .= " ORDER BY $helpdeskcolumn2[ticket_subject]";
                break;
        }
        if($cboSearch2>0)
            {
            switch ($cboSearch2) {
                case "1":
                    $sql .= ", $helpdeskcolumn2[ticket_lastupdate]";
                    break;
                case "3":
                    $sql .= ", $helpdeskcolumn2[ticket_date]";
                    break;
                case "2":
                    $sql .= ", $helpdeskcolumn2[ticket_subject]";
                    break;
            }
        }
    }
    //return $sql;
    $results = $dbconn->Execute($sql);

    if ($results === false) {
        return false;
    }
    
    $editaccess = xarSecurityCheck('edithelpdesk');
    $searchresults = array();
    for (; !$results->EOF; $results->MoveNext()) {
        list($ticket_id,$hticket_id,$ticket_subject,$ticket_lastupdate,$ticket_date) = $results->fields;
        // Ticket will only be added to the array if user has Edit access OR
        // if the user is the person who submitted the ticket
        if(xarModAPIFunc('helpdesk', 'user', 'isticketowner', array('ticket_id' => $ticket_id)) || $editaccess ){

            $searchresults[] = array(
                    'ticket_id'         => $ticket_id,
                    'ticket_subject'    => xarVarPrepForDisplay($ticket_subject),
                    'ticket_date'       => xarModAPIFunc('helpdesk', 'user', 'formatdate', array('ticket_date' => $ticket_date))
                    'ticket_lastupdate' => xarModAPIFunc('helpdesk', 'user', 'formatdate', array('ticket_date' => $ticket_date))
                );
        }
    }
    $results->close();
    return $searchresults;
}
?>

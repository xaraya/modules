<?php

function xproject_tasksapi_countitems($args)
{
	extract($args);
	
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();


    $xprojecttable = $xartable['xproject_tasks'];

	$sql = "SELECT COUNT(1)
			FROM $xprojecttable";
	if(isset($parentid)) {
		$sql .= " WHERE xar_parentid = $parentid";
	} elseif(isset($projectid)) {
		$sql .= " WHERE xar_projectid = $projectid";
	}
	
    $result = $dbconn->Execute($sql);

    if ($dbconn->ErrorNo() != 0) {
        return;
    }

    list($numtasks) = $result->fields;

    $result->Close();

    return $numtasks;
}

function makeSearchQuery($wildcards,$priority, $status, $project, $responsible_persons,$order_by,$date_min,$date_max)
{
    global $abfrage;

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    /* Generate the SQL-Statement */
    $xproject_todos_column = &$xartable['xproject_todos_column'];
    $xproject_responsible_persons_column = &$xartable['xproject_responsible_persons_column'];
    $xproject_notes_column = &$xartable['xproject_notes_column'];

    $query="SELECT $xartable[xproject_todos].*, count($xproject_notes_column[todo_id]) AS nr_notes
        FROM $xartable[xproject_todos], $xartable[xproject_responsible_persons]
        LEFT JOIN $xartable[xproject_notes]
        ON $xproject_todos_column[todo_id]=$xproject_notes_column[todo_id]
        WHERE $xproject_todos_column[todo_text] LIKE ";

    if ($wildcards) {
        $query=$query . "'%$abfrage%' "; 
    } else {
        $query=$query . "'$abfrage' "; 
    }

    if ($priority!=""){
        $query=$query . "AND $xproject_todos_column[todo_priority]=$priority "; 
    }

    if ($status!="" && $status != "all"){
        $query=$query . "AND $xproject_todos_column[status]=$status "; 
    }

    if ($project!=""){
        if ($project != "all") {
            $query=$query . "AND $xproject_todos_column[project_id]=$project "; 
        } else {
            $xproject_project_members_column = &$xartable['xproject_project_members_column'];
            $sql2 = "SELECT $xproject_project_members_column[project_id]
               FROM $xartable[xproject_project_members]
               WHERE $xproject_project_members_column[member_id]=".
               xarUserGetVar('uid')."";
            $result = $dbconn->Execute($sql2);

            for (;!$result->EOF;$result->MoveNext()){
                $tasks[] = $result->fields[0];
            }
            if ($tasks[0]!="") {
                $query.=" AND $xproject_todos_column[project_id] in (";

                        while ($neu=array_pop($tasks)){
                        $query .= $neu;
                        if (sizeof($tasks) > 0)
                        $query .= ',';
                        else
                        $query .= ') ';
                        }
            }

        }
    }

    if ( ereg( "([0-9]{1,2})([.-/]{0,1})([0-9]{1,2})([.-/]{0,1})([0-9]{2,4})", trim($date_min), $regs ) ) {
        $date_min = mktime(0,0,0,$regs[1],$regs[2],$regs[0]);
    }
    if ( ereg( "([0-9]{1,2})([.-/]{0,1})([0-9]{1,2})([.-/]{0,1})([0-9]{2,4})", trim($date_max), $regs ) ) {
        $date_max = mktime(0,0,0,$regs[1],$regs[2],$regs[0]);
    }
    if (!$date_min){
        $date_min = "0";
    }

    if (!$date_max){
        $date_max = time();
    }

/*
    if (xarModGetVar('xproject', 'DATEFORMAT') != "1" ) {
        $date_min=convDateToUS($date_min);
        $date_max=convDateToUS($date_max);
    }
    if (!$date_min){ $date_min = "0000-00-00"; }
    if (!$date_max){ $date_max = date("Y-m-d");}
*/

    $query=$query . "AND $xproject_todos_column[date_changed] >= '$date_min'
    AND $xproject_todos_column[date_changed] <= '$date_max' ";

    /* sizeof(array) > 0 doesn't work? */
    if ($responsible_persons[0]!="") {
        $query.=" AND $xproject_responsible_persons_column[user_id] in (";

                while ($neu=array_pop($responsible_persons)){
                $query .= $neu;
                if (sizeof($responsible_persons) > 0)
                $query .= ',';
                else
                $query .= ') ';
                }
    }
    $query .= "AND $xproject_responsible_persons_column[todo_id]=$xproject_todos_column[todo_id]";

    $query=$query . " GROUP BY $xproject_todos_column[todo_id] ";

    // How should the table be ordered?
    $query .= orderBy($order_by);
    return $query;
}
// end makeSearchQuery

?>
<?php
/**
 * Create a new task
 *
 */
function tasks_adminapi_create($args)
{
    extract($args);

    if (empty($name)) {
        xarSessionSetVar('errormsg', xarGetStatusMsg() . '<br>tasks_adminapi_create: ' . xarML("Module argument error"));
        return false;
    }
	
//     if (!xarSecAuthAction(0, 'tasks::task', '$task[modname]:$task[objectid]:$task[basetaskid]', ACCESS_COMMENT)) {
//         xarSessionSetVar('errormsg', xarGetStatusMsg() . '<br>tasks_adminapi_create: ' . _TASKS_NOAUTH);
//         return false;
//     }
		
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    $taskstable = $xartable['tasks'];

    $nextId = $dbconn->GenId($taskstable);

    $sql = "INSERT INTO $taskstable (
              xar_id,
			   xar_parentid,
			   xar_modname,
			   xar_objectid,
			   xar_name,
			   xar_status,
			   xar_priority,
			   xar_description,
			   xar_private,
			   xar_creator,
			   xar_owner,
			   xar_assigner,
			   xar_date_created,
			   xar_date_approved,
			   xar_date_changed,
			   xar_date_start_planned,
			   xar_date_start_actual,
			   xar_date_end_planned,
			   xar_date_end_actual,
			   xar_hours_planned,
			   xar_hours_spent,
			   xar_hours_remaining)
            VALUES (
              $nextId,
			  " . ($parentid ? $parentid : 0) . ",
			  '" . xarVarPrepForStore($modname) . "',
			  " . ($objectid ? $objectid : 0) . ",
              '" . xarVarPrepForStore($name) . "',
              " . xarVarPrepForStore($status) . ",
              " . xarVarPrepForStore($priority) . ",
              '" . xarVarPrepForStore($description) . "',
              " . ($private ? $private : "0") . ",
              " . xarSessionGetVar('uid') . ",
              " . xarSessionGetVar('uid') . ",
              " . xarSessionGetVar('uid') . ",
              " . time() . ",
			  0,
			  " . time() . ",
			  0,
			  0,
			  0,
			  0,
			  '',
			  '',
			  '')";			  

    // PRIVATE INITIALLY SET BASED ON USER PREFERENCE
    
    $res =& $dbconn->Execute($sql);
    if (!$res) return;

    $id = $dbconn->PO_Insert_ID($taskstable, 'xar_id');

    $returnid = (xarModGetVar('tasks','returnfromedit') ? $id : $parentid);
	return $returnid;
}

?>
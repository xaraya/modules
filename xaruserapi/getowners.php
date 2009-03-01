<?php

/**
 *
 *
 * Administration System
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2002 by the Xaraya Development Team.
 * @link http://www.xaraya.com
 *
 * @subpackage xtasks module
 * @author Chad Kraeft <stego@xaraya.com>
*/
function xtasks_userapi_getowners($args)
{
    extract($args);
    
    if(!empty($modname)) {
        $modid = xarModGetIDFromName($modname);
    }
    
    $show_project = xarModGetUserVar('xtasks', 'show_project');
    $show_client = xarModGetUserVar('xtasks', 'show_client');
    
    if (!isset($mode)) {
        $mode = "";
    }
    if (!isset($parentid)
        && !isset($projectid)
        && (!isset($modid) || !isset($objectid))) {
        $parentid = '0';
    }
    if (!isset($q)) {
        $q = "";
    }

    if (!xarSecurityCheck('ViewXTask', 0, 'Item', "All:All:All")) {//TODO: security
        $msg = xarML('Not authorized to access #(1) items',
                    'xtasks');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'NO_PERMISSION',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
    }
    
    xarModDBInfoLoad('dossier');

    $dbconn =& xarDBGetConn();
    $xartable = xarDBGetTables();

    $xtaskstable = $xartable['xtasks'];
    $contactstable = $xartable['dossier_contacts'];

    $sql = "SELECT DISTINCT b.contactid,
                  b.cat_id,
                  b.ownerid,
                  b.userid,
                  b.private,
                  b.contactcode,
                  b.prefix,
                  b.lname,
                  b.fname,
                  b.sortname,
                  b.dateofbirth,
                  b.title,
                  b.company,
                  b.sortcompany,
                  b.img,
                  b.phone_work,
                  b.phone_cell,
                  b.phone_fax,
                  b.phone_home,
                  b.email_1,
                  b.email_2,
                  b.chat_AIM,
                  b.chat_YIM,
                  b.chat_MSNM,
                  b.chat_ICQ,
                  b.contactpref,
                  b.notes,
                  b.datecreated,
                  b.datemodified
            FROM $xtaskstable a, $contactstable b";
            
    $whereclause = array();
    
    $whereclause[] = "a.owner = b.contactid";
            
    if(isset($creator) && $creator > 0) {
        $whereclause[] = "a.creator = ".$creator;
    }
            
    if(isset($assigner) && $assigner > 0) {
        $whereclause[] = "a.assigner = ".$assigner;
    }
            
    if (!empty($modid) 
        && !empty($objectid)
        && $modid == xarModGetIDFromName('xtasks')) {
        $parentid = $objectid;
//        $modid = 0;
//        $objectid = 0;
    }
    
    if (!empty($projectid)) {
        $whereclause[] = "a.projectid=".$projectid;
    } elseif (!empty($modid)) {
        $hookedsql = "( a.modid=".$modid;
        if (!empty($objectid)) {
            $hookedsql .= " AND a.objectid=".$objectid;
        }
        if (!empty($itemtype)) {
            $hookedsql .= " AND a.itemtype=".$itemtype;
        }
        $hookedsql .= " )";
            
        if (!empty($parentid)) {
            $hookedsql .= " OR a.parentid=".$parentid;
        }
        $whereclause[] = $hookedsql;
    } elseif (!empty($parentid)) {
        $whereclause[] = "a.parentid=".$parentid;
    }
            
    if ($mode == "Open") {
        $whereclause[] = "a.status != 'Closed'";
    } elseif (!empty($statusfilter)) {
        $whereclause[] = "a.status='".$statusfilter."'";
    } else {
        $statusfilter = "";
    }
    if(!empty($q)) {
        $whereclause[] = "(a.task_name LIKE '%".$q."%' OR a.description LIKE '%".$q."%')";
    }    
    
    if(count($whereclause) > 0) $sql .= " WHERE ".implode(" AND ", $whereclause);

    $sql .= " ORDER BY b.sortcompany, b.fname, b.lname";
/*
    if ($selected_project != "all") {
        $sql .= " AND $xtasks_todos_column[project_id]=".$selected_project;

    if (xarSessionGetVar('xtasks_my_tasks') == 1 ) {
        // show only tasks where I'm responsible for
        $query .= "
            AND $xtasks_responsible_persons_column[user_id] = ".xarUserGetVar('uid')."
            AND $xtasks_todos_column[todo_id] = $xtasks_responsible_persons_column[todo_id]";
    }

    // WHERE CLAUSE TO NOT PULL IF TASK IS PRIVATE AND USER IS NOT OWNER, CREATOR, ASSIGNER, OR ADMIN
    // CLAUSE TO FILTER BY STATUS, MIN PRIORITY, OR DATES
    // CLAUSE WHERE USER IS OWNER
    // CLAUSE WHERE USER IS CREATOR
    // CLAUSE WHERE USER IS ASSIGNER
    // CLAUSE FOR ACTIVE ONLY (ie. started but not yet completed)
    // CLAUSE BY TEAM/GROUPID (always on?)
    //
    // CLAUSE TO PULL PARENT TASK SETS
    // or
    // USERAPI_GET FOR EACH PARENT LEVEL
*/

    $result = $dbconn->Execute($sql);

    if ($dbconn->ErrorNo() != 0) {
        $msg = xarML('DATABASE_ERROR'. $sql);
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'DATABASE_ERROR: ',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
    }

    $tasks = array();

    for (; !$result->EOF; $result->MoveNext()) {
        list($contactid,
            $cat_id,
            $ownerid,
            $userid,
            $private,
            $contactcode,
            $prefix,
            $lname,
            $fname,
            $sortname,
            $dateofbirth,
            $title,
            $company,
            $sortcompany,
            $img,
            $phone_work,
            $phone_cell,
            $phone_fax,
            $phone_home,
            $email_1,
            $email_2,
            $chat_AIM,
            $chat_YIM,
            $chat_MSNM,
            $chat_ICQ,
            $contactpref,
            $notes,
            $datecreated,
            $datemodified) = $result->fields;
            
            
        $ownerlist[$contactid] = array(
                        'contactid'     => $contactid,
                        'cat_id'      => $cat_id,
                        'ownerid'     => $ownerid,
                        'userid'      => $userid,
                        'private'     => $private,
                        'contactcode' => $contactcode,
                        'prefix'      => $prefix,
                        'lname'       => $lname,
                        'fname'       => $fname,
                        'sortname'    => $sortname,
                        'dateofbirth' => $dateofbirth,
                        'title'       => $title,
                        'company'     => $company,
                        'sortcompany' => $sortcompany,
                        'img'         => $img,
                        'phone_work'  => $phone_work,
                        'phone_cell'  => $phone_cell,
                        'phone_fax'   => $phone_fax,
                        'phone_home'  => $phone_home,
                        'email_1'     => $email_1,
                        'email_2'     => $email_2,
                        'chat_AIM'    => $chat_AIM,
                        'chat_YIM'    => $chat_YIM,
                        'chat_MSNM'   => $chat_MSNM,
                        'chat_ICQ'    => $chat_ICQ,
                        'contactpref' => $contactpref,
                        'notes'       => $notes,
                        'datecreated' => $datecreated,
                        'datemodified'=> $datemodified);
    }

    $result->Close();
//echo $sql."<pre>"; print_r($ownerlist); echo "</pre>";
    return $ownerlist;
}

?>
<?php
/**
 * Access Methods Module
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Access Methods Module
 * @link http://xaraya.com/index.php/release/333.html
 * @author St.Ego <webmaster@ivory-tower.net>
 */
function accessmethods_userapi_getall($args)
{
    extract($args);

    if (!isset($startnum)) {
        $startnum = 1;
    }
    if (!isset($numitems)) {
        $numitems = -1;
    }

    $invalid = array();
    if (!isset($startnum) || !is_numeric($startnum)) {
        $invalid[] = 'startnum';
    }
    if (!isset($numitems) || !is_numeric($numitems)) {
        $invalid[] = 'numitems';
    }
    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    join(', ',$invalid), 'user', 'getall', 'accessmethods');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
    }

    if (!xarSecurityCheck('ViewXProject', 0, 'Item', "All:All:All")) {//TODO: security
        $msg = xarML('Not authorized to access #(1) items',
                    'accessmethods');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'NO_PERMISSION',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
    }

    $dbconn =& xarDBGetConn();
    $xartable = xarDBGetTables();

    $accessmethodstable = $xartable['accessmethods'];

    $sql = "SELECT siteid,
                  clientid,
                  webmasterid,
                  site_name,
                  url,
                  description,
                  accesstype,
                  sla,
                  accesslogin,
                  accesspwd,
                  related_sites
            FROM $accessmethodstable";
            
            
//	$sql .= " WHERE $taskcolumn[parentid] = $parentid";
//	$sql .= " AND $taskcolumn[projectid] = $projectid";
//	if($groupid > 0) $sql .= " AND $taskcolumn[groupid] = $groupid";
    $sql .= " ORDER BY accesstype, site_name";

/*
    if ($selected_project != "all") {
        $sql .= " AND $accessmethods_todos_column[project_id]=".$selected_project;

    if (xarSessionGetVar('accessmethods_my_tasks') == 1 ) {
        // show only tasks where I'm responsible for
        $query .= "
            AND $accessmethods_responsible_persons_column[user_id] = ".xarUserGetVar('uid')."
            AND $accessmethods_todos_column[todo_id] = $accessmethods_responsible_persons_column[todo_id]";
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

    $result = $dbconn->SelectLimit($sql, $numitems, $startnum-1);

    if ($dbconn->ErrorNo() != 0) {
        $msg = xarML('DATABASE_ERROR: '.$sql);
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'DATABASE_ERROR',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
    }

    $sitelist = array();

    for (; !$result->EOF; $result->MoveNext()) {
        list($siteid,
              $clientid,
              $webmasterid,
              $site_name,
              $url,
              $description,
              $accesstype,
              $sla,
              $accesslogin,
              $accesspwd,
              $related_sites) = $result->fields;
        if (xarSecurityCheck('ReadAccessMethods', 0, 'Item', "$site_name:All:$siteid")) {
            $sitelist[] = array('siteid'        => $siteid,
                              'clientid'        => $clientid,
                              'webmasterid'     => $webmasterid,
                              'site_name'       => $site_name,
                              'url'             => $url,
                              'description'     => $description,
                              'accesstype'        => $accesstype,
                              'sla'             => $sla,
                              'accesslogin'        => $accesslogin,
                              'accesspwd'        => $accesspwd,
                              'related_sites'   => $related_sites);
        }
    }

    $result->Close();

    return $sitelist;
}

?>

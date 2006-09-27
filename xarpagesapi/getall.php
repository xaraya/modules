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
 * @subpackage xproject module
 * @author Chad Kraeft <stego@xaraya.com>
*/
function xproject_pagesapi_getall($args)
{
    extract($args);

    $invalid = array();
    if ((!isset($projectid) || !is_numeric($projectid)) && (!isset($parentid) || !is_numeric($parentid))) {
        $invalid[] = 'projectid or parentid';
    }
    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    join(', ',$invalid), 'pages', 'getall', 'xProject');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
    }

    if (!xarSecurityCheck('ViewXProject', 0, 'Item', "All:All:All")) {//TODO: security
        $msg = xarML('Not authorized to access #(1) items',
                    'xproject');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'NO_PERMISSION',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
    }

    $dbconn =& xarDBGetConn();
    $xartable = xarDBGetTables();

    $projectstable = $xartable['xProjects'];
    $pagestable = $xartable['xProject_pages'];

    $sql = "SELECT pageid,
                  parentid,
                  page_name,
                  $pagestable.projectid,
                  $projectstable.project_name,
                  sequence,
                  $pagestable.status,
                  $pagestable.description,
                  relativeurl
            FROM $pagestable, $projectstable
            WHERE $projectstable.projectid = $pagestable.projectid
            ".(isset($projectid) ? " AND $pagestable.projectid = $projectid " : "")."
            ".(isset($parentid) ? " AND $pagestable.parentid = $parentid " : "")."
            ORDER BY parentid, sequence, page_name";

    $result = $dbconn->Execute($sql);

    if (!$result) return;
    
    $items = array();

    for (; !$result->EOF; $result->MoveNext()) {
        list($pageid,
              $thisparentid,
              $page_name,
              $projectid,
              $project_name,
              $sequence,
              $status,
              $description,
              $relativeurl) = $result->fields;
        if (xarSecurityCheck('ReadXProject', 0, 'Item', "$project_name:All:$projectid")) {
            $children = array();
            if(isset($parentid)) {
                $children = xarModAPIFunc('xproject', 'pages', 'getall', array('projectid' => $projectid, 'parentid' => $pageid));
            }                        
            $items[$pageid] = array(
                            'pageid'           => $pageid,
                            'parentid'         => $thisparentid,
                            'page_name'        => $page_name,
                            'projectid'        => $projectid,
                            'project_name'     => $project_name,
                            'status'           => $status,
                            'sequence'         => $sequence,
                            'description'      => $description,
                            'relativeurl'      => $relativeurl,
                            'children'         => $children);
        }
    }

    $result->Close();

    return $items;
}

?>
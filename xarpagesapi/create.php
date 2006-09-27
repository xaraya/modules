<?php
/**
 * Administration System
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2002 by the Xaraya Development Team.
 * @link http://www.xaraya.com
 *
 * @subpackage xproject module
 * @author Chad Kraeft <stego@xaraya.com>
*/
function xproject_pagesapi_create($args)
{
    extract($args);

    $invalid = array();
    if (!isset($page_name) || !is_string($page_name)) {
        $invalid[] = 'page_name';
    }
    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    join(', ',$invalid), 'pages', 'create', 'xproject');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return;
    }

    if (!xarSecurityCheck('AddXProject', 1, 'Item', "All:All:All")) {
        $msg = xarML('Not authorized to add #(1) items',
                    'xproject');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'NO_PERMISSION', new SystemException($msg));
        return;
    }

    if(empty($sequence)) {
        $ttlpages = xarModAPIFunc('xproject', 'pages', 'getall', array('projectid' => $projectid,'parentid' => $parentid));
        $sequence = count($ttlpages) + 1;
    }

    $dbconn =& xarDBGetConn();
    $xartable = xarDBGetTables();

    $pagetable = $xartable['xProject_pages'];

    $nextId = $dbconn->GenId($pagetable);

    $query = "INSERT INTO $pagetable (
                  pageid,
                  page_name,
                  parentid,
                  projectid,
                  status,
                  sequence,
                  description,
                  relativeurl)
            VALUES (?,?,?,?,?,?,?,?)";

    $bindvars = array(
              $nextId,
              $page_name,
              $parentid,
              $projectid,
              $status,
              $sequence,
              $description,
              $relativeurl);

    $result = &$dbconn->Execute($query,$bindvars);
    if (!$result) return;

    if((int)$sequence == $sequence) {
        xarModAPIFunc('xproject', 'pages', 'sequence', array('projectid' => $projectid));
    }

    $logdetails = "Page created: ".$page_name.".";
    $logid = xarModAPIFunc('xproject',
                        'log',
                        'create',
                        array('projectid'   => $projectid,
                            'userid'        => xarUserGetVar('uid'),
                            'details'        => $logdetails,
                            'changetype'    => "PAGE"));

    $pageid = $dbconn->PO_Insert_ID($pagetable, 'pageid');

    return $pageid;
}

?>
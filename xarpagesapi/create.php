<?php
/**
 * XProject Module - A simple project management module
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage XProject Module
 * @link http://xaraya.com/index.php/release/665.html
 * @author XProject Module Development Team
 */
/**
 * Administration System
 *
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

    if(!isset($sequence)) {
        $ttlpages = xarModAPIFunc('xproject', 'pages', 'getall', array('projectid' => $projectid));
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
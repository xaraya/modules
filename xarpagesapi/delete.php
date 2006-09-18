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
function xproject_pagesapi_delete($args)
{
    extract($args);

    if (!isset($pageid) || !is_numeric($pageid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    'feature ID', 'features', 'delete', 'xproject');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return;
    }

    // does it exist ?
    $item = xarModAPIFunc('xproject',
                            'pages',
                            'get',
                            array('pageid' => $pageid));

    if (!isset($item) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return;

    if (!xarSecurityCheck('DeleteXProject', 1, 'Item', "$item[project_name]:All:$item[projectid]")) {
        return;
    }

    $dbconn =& xarDBGetConn();
    $xartable = xarDBGetTables();

    $pagestable = $xartable['xProject_pages'];

    // does it have children ?
    $sql = "DELETE FROM $pagestable
            WHERE pageid = " . $pageid;
    $result = $dbconn->Execute($sql);

    /* Check for an error with the database code, adodb has already raised
     * the exception so we just return
     */
    if (!$result) return;

    xarModAPIFunc('xproject', 'pages', 'sequence', array('projectid' => $item['projectid']));

    $logdetails = "Page removed: ".$item['page_name'].".";
    $logid = xarModAPIFunc('xproject',
                        'log',
                        'create',
                        array('projectid'   => $item['projectid'],
                            'userid'        => xarUserGetVar('uid'),
                            'details'        => $logdetails,
                            'changetype'    => "PAGE"));

    // Let the calling process know that we have finished successfully
    return true;
}

?>

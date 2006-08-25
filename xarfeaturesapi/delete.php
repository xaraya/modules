<?php
/**
 * XProject Module - A simple project management module
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage XProject Module
 * @link http://xaraya.com/index.php/release/665.html
 * @author XProject Module Development Team
 */
function xproject_featuresapi_delete($args)
{
    extract($args);

    if (!isset($featureid) || !is_numeric($featureid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    'feature ID', 'features', 'delete', 'xproject');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return;
    }

    // does it exist ?
    $item = xarModAPIFunc('xproject',
                            'features',
                            'get',
                            array('featureid' => $featureid));

    if (!isset($item) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return;

    if (!xarSecurityCheck('DeleteXProject', 1, 'Item', "$item[project_name]:All:$item[projectid]")) {
        $msg = xarML('Not authorized to delete #(1) item #(2)',
                    'xproject', xarVarPrepForStore($projectid));
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'NO_PERMISSION',
                       new SystemException($msg));
        return;
    }

    $dbconn =& xarDBGetConn();
    $xartable = xarDBGetTables();

    $featurestable = $xartable['xProject_features'];
    
    $sql = "DELETE FROM $featurestable
            WHERE featureid = " . $featureid;
    $result = $dbconn->Execute($sql);

    if ($dbconn->ErrorNo() != 0) {
        $msg = xarML('DATABASE_ERROR', $sql);
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'DATABASE_ERROR',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
    }

    $logdetails = "Feature removed: ".$item['feature_name'].".";
    $logid = xarModAPIFunc('xproject',
                        'log',
                        'create',
                        array('projectid'   => $item['projectid'],
                            'userid'        => xarUserGetVar('uid'),
                            'details'	    => $logdetails,
                            'changetype'	=> "FEATURE"));

    // Let the calling process know that we have finished successfully
    return true;
}

?>

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
 * Update an item
 *
 * @author the xProject module development team
 * @param  $args ['exid'] the ID of the item
 * @param  $args ['name'] the new name of the item
 * @param  $args ['number'] the new number of the item
 * @raise BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
 */
function xproject_featuresapi_update($args)
{
    extract($args);

    $invalid = array();
    if (!isset($featureid) || !is_numeric($featureid)) {
        $invalid[] = 'feature ID';
    }
    if (!isset($feature_name) || !is_string($feature_name)) {
        $invalid[] = 'feature_name';
    }
    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
            join(', ', $invalid), 'features', 'update', 'xproject');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
            new SystemException($msg));
        return;
    }

    $item = xarModAPIFunc('xproject',
                            'features',
                            'get',
                            array('featureid' => $featureid));

    if (!isset($item) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; // throw back

    if (!xarSecurityCheck('EditXProject', 1, 'Item', "$item[project_name]:All:$item[projectid]")) {
        return;
    }

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    $featurestable = $xartable['xProject_features'];

    $query = "UPDATE $featurestable
            SET feature_name =?,
                  importance = ?,
                  details = ?,
                  tech_notes = ?,
                  date_approved = ?,
                  date_available = ?
            WHERE featureid = ?";

    $bindvars = array(
              $feature_name,
              $importance,
              $details,
              $tech_notes,
              $date_approved,
              $date_available,
              $featureid);

    $result = &$dbconn->Execute($query,$bindvars);

    if (!$result) { // return;
        $msg = xarML('SQL: #(1)',
            $dbconn->ErrorMsg());
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
            new SystemException($msg));
        return;
    }

    $logdetails = "Feature modified: ".$item['feature_name'].".";
    $logid = xarModAPIFunc('xproject',
                        'log',
                        'create',
                        array('projectid'   => $item['projectid'],
                            'userid'        => xarUserGetVar('uid'),
                            'details'        => $logdetails,
                            'changetype'    => "FEATURE"));

    return true;
}
?>
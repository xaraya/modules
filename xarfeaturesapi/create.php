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
function xproject_featuresapi_create($args)
{
    extract($args);

    $invalid = array();
    if (!isset($feature_name) || !is_string($feature_name)) {
        $invalid[] = 'feature_name';
    }
    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    join(', ',$invalid), 'admin', 'create', 'xproject');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return;
    }

    if (!xarSecurityCheck('AddXProject', 1, 'Item', "All:All:All")) {
        $msg = xarML('Not authorized to add #(1) items',
                    'xproject');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'NO_PERMISSION', new SystemException($msg));
        return;
    }

    $dbconn =& xarDBGetConn();
    $xartable = xarDBGetTables();

    $featuretable = $xartable['xProject_features'];

    $nextId = $dbconn->GenId($featuretable);

    $query = "INSERT INTO $featuretable (
                  featureid,
                  feature_name,
                  projectid,
                  details,
                  tech_notes,
                  importance,
                  date_approved,
                  date_available)
            VALUES (?,?,?,?,?,?,?,?)";

    $bindvars = array(
              $nextId,
              $feature_name,
              $projectid,
              $details,
              $tech_notes,
              $importance,
              $date_approved,
              $date_available);

    $result = &$dbconn->Execute($query,$bindvars);
    if (!$result) return;

// PRIVATE INITIALLY SET BASED ON USER PREFERENCE

    $logdetails = "Feature added: ".$feature_name.".";
    $logid = xarModAPIFunc('xproject',
                        'log',
                        'create',
                        array('projectid'   => $projectid,
                            'userid'        => xarUserGetVar('uid'),
                            'details'        => $logdetails,
                            'changetype'    => "FEATURE"));


    $featureid = $dbconn->PO_Insert_ID($featuretable, 'featureid');

    return $featureid;
}

?>
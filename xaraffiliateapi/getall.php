<?php
/**
 * LabAffiliate Module - initialization functions
 *
 * @package modules
 * @copyright (C) 2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage LabAffiliate Module
 * @link http://xaraya.com/index.php/release/919
 * @author LabAffiliate Module Development Team
 */
function labaffiliate_affiliateapi_getall($args)
{
    extract($args);

    if (!isset($startnum)) {
        $startnum = 1;
    }
    if (!isset($numitems)) {
        $numitems = -1;
    }

	$invalid = array();

    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
            join(', ', $invalid), 'user', 'getall', 'labAffiliate');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
            new SystemException($msg));
        return;
    }

    $items = array();

    if (!xarSecurityCheck('ViewProgramAffiliate')) return;

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

	$programs_table = $xartable['labaffiliate_programs'];
	$affiliates_table = $xartable['labaffiliate_affiliates'];

	$sql = "SELECT
				a.xar_affiliateid,
				a.xar_uplineid,
                b.xar_userid,
				a.xar_userid,
				a.xar_primaryprogramid,
                c.xar_program_name,
				a.xar_secondaryprogramid,
                d.xar_program_name,
                a.xar_status,
                a.xar_marketing_copy,
                COUNT(e.xar_affiliateid)
			FROM
				($affiliates_table a, $affiliates_table b)
            LEFT JOIN $programs_table c
            ON c.xar_programid = a.xar_primaryprogramid
            LEFT JOIN $programs_table d
            ON d.xar_programid = a.xar_secondaryprogramid
            LEFT JOIN $affiliates_table e
            ON e.xar_uplineid = a.xar_affiliateid";
            
            
    $bindvars = array();
    if(!empty($uplineid)) {
	    $sql .= " WHERE a.xar_uplineid = ?";
        $bindvars[] = $uplineid;
    }
            
            
    $sql .= " GROUP BY a.xar_affiliateid
            ORDER BY a.xar_uplineid";

    $result = $dbconn->SelectLimit($sql, $numitems, $startnum-1, $bindvars);

	if (!$result) return;

    for (; !$result->EOF; $result->MoveNext()) {
        list($affiliateid,
            $uplineid,
            $uplineuserid,
            $userid,
            $primaryprogramid,
            $primaryprogramname,
            $secondaryprogramid,
            $secondaryprogramname,
            $status,
            $marketing_copy,
            $ttldownline) = $result->fields;
        if (xarSecurityCheck('ViewProgramAffiliate', 0, 'Affiliate', "$affiliateid:All:$userid")) {
            $items[] = array('affiliateid'              => $affiliateid,
								'uplineid'              => $uplineid,
								'uplineuserid'          => $uplineuserid,
								'userid'                => $userid,
								'primaryprogramid'      => $primaryprogramid,
								'primaryprogramname'    => $primaryprogramname,
								'secondaryprogramid'    => $secondaryprogramid,
								'secondaryprogramname'  => $secondaryprogramname,
								'status'                => $status,
								'marketing_copy'        => $marketing_copy,
								'ttldownline'           => $ttldownline);
        }
    }

    $result->Close();

    return $items;
}

?>

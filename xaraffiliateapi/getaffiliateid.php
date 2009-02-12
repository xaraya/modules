<?php

function labaffiliate_affiliateapi_getaffiliateid($args)
{
    extract($args);

    if (!isset($userid) || !is_numeric($userid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
            'User ID', 'affiliate', 'getaffiliateid', 'labAffiliate');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
            new SystemException($msg));
        return;
    }

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

	$labaffiliate_affiliates_table = $xartable['labaffiliate_affiliates'];

    $query = "SELECT 
				xar_affiliateid,
				xar_uplineid,
				xar_userid,
				xar_primaryprogramid,
				xar_secondaryprogramid
              FROM $labaffiliate_affiliates_table
              WHERE xar_userid = ?";
    $result = &$dbconn->Execute($query,array($userid));

    if (!$result) return;

    if ($result->EOF) return 0;

    list($affiliateid,$uplineid,$userid,$primaryprogramid,$secondaryprogramid) = $result->fields;

    $result->Close();

    if (!xarSecurityCheck('ReadProgramAffiliate', 1, 'Affiliate', "All:All:$affiliateid")) {
        return;
    }
    
    return $affiliateid;
}

?>
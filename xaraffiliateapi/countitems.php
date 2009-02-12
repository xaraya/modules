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
function labaffiliate_affiliateapi_countitems($args)
{
	extract($args);

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

	$labaffiliate_affiliates_table = $xartable['labaffiliate_affiliates'];

    $query = "SELECT COUNT(1)
            FROM $labaffiliate_affiliates_table";

	$bindvars = array();

	if($isset($userid) && is_numeric($userid)){
		$query .= " WHERE xar_userid =?";
		$bindvars[] = $userid;
	}

    $result = &$dbconn->Execute($query,$bindvars);

    if (!$result) return;

    list($numitems) = $result->fields;

    $result->Close();

    return $numitems;
}

?>
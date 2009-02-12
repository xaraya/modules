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
function labaffiliate_userapi_countitems()
{
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

	$labaffiliate_programs_table = $xartable['labaffiliate_programs'];

    $sql = "SELECT COUNT(1)
            FROM $labaffiliate_programs_table";

    $result = &$dbconn->Execute($sql,array());

    if (!$result) return;

    list($numitems) = $result->fields;

    $result->Close();

    return $numitems;
}
?>
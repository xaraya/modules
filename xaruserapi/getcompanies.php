<?php
/**
 * DOSSIER userapi getCompanies
 *
 * @package modules
 * @copyright (C) 2002-2007 Chad Kraeft
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Dossier Module
 * @author Chad Kraeft <cdavidkraeft@miragelab.com>
 * Based on labDossier (on PostNuke) by Chad Kraeft <cdavidkraeft@miragelab.com>
 */

/**
 * getCompanies
 *
 * @param N/A
 * @return array of companies
 */
function dossier_userapi_getcompanies($args)
{
    extract($args);
    
    if (!isset($ownerid) || !is_numeric($ownerid)) {
        $ownerid = 0;
    }
    if (!isset($userid) || !is_numeric($userid)) {
        $userid = 0;
    }
    
    $dbconn =& xarDBGetConn();
    $xarTables =& xarDBGetTables();
    $contacts_table = $xarTables['dossier_contacts'];

    $sql = "SELECT DISTINCT company
            FROM $contacts_table
            WHERE company != ''";
            
    $bindvars = array();
    
    if($ownerid > 0) {
        $sql .= " AND ownerid = ?";
        $bindvars[] = $ownerid;
    } elseif($userid > 0) {
        $sql .= " AND userid = ?";
        $bindvars[] = $userid;
    }
    
    $sql .= " ORDER BY sortcompany, company";
    
    $result =& $dbconn->Execute($sql, $bindvars);

    if($dbconn->ErrorNo() != 0) { return array(); }
    if(!isset($result)) { return array(); }

    $companies = array(); // 'id'=>'0','name'=>xarML('Enter new company name or select a company...'));
    for($i=1; !$result->EOF; $result->MoveNext()) {
        list($company) = $result->fields;
        $companies[]     = array('id'=>str_replace(" ", "+", trim($company)),'name'=>$company);
     }
    $result->Close();
    return $companies;

} // END getCompanies

?>

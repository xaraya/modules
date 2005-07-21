<?php
/*/
 * removereco
 * removes a reco from the database
 *
 * @returns true or false
/*/
function shopping_adminapi_removereco($args)
{
    // security check
    if (!xarSecurityCheck('DeleteShoppingRecos')) return false;

    // get args
    extract($args);
    
    if (!isset($rid) && !isset($iid) && !isset($twoiid)) return;
    if (isset($twoidd) && (!isset($iid1) || !isset($iid2))) return;

    // get databse setup
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $recostable = $xartable['shopping_recommendations'];

    // set up query and run it
    $sql = "DELETE FROM $recostable";

    if (isset($rid)) {
        $sql .= " WHERE xar_rid = ?";
        $bindvars[] = array($rid);
    } elseif (isset($iid)) {
      $sql .= " WHERE xar_iid1 = ? OR xar_iid2 = ?";
        $bindvars = array ($iid, $iid);
    } elseif (isset($twoiid)) {
      $sql .= " WHERE (xar_iid1 = ? AND xar_iid2 = ?)
                OR    (xar_iid1 = ? AND xar_iid2 = ?)";
        $bindvars = array($iid1, $iid2, $iid2, $iid1);
    }

    $result = &$dbconn->Execute($sql,$bindvars);
    if (!$result) return false;

    return true;
}
?>
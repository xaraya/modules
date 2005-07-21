<?php
/*/
 * createreco
 * creates a new reco from the info recieved from addreco
 *
 * @redirects you to displayreco (func=displayitem&iid=iid&phase=4)
/*/
function shopping_userapi_createreco($args)
{
    // security check
    if (!(xarSecurityCheck('DeleteShoppingRecos', 0) || (xarSecurityCheck('SubmitShoppingRecos') && xarModGetVar('shopping', 'userecommendations')))) return;

    extract($args);

    // get the current userid from the database
    $uname = xarUserGetVar('uname');

      // get database setup and items table
      $dbconn =& xarDBGetConn();
      $xartable =& xarDBGetTables();
      $recostable = $xartable['shopping_recommendations'];

      // loop through all selected recos
      foreach ($recos as $iid2) {
        // get the next available auto-increment value from the recos table
        $rid = $dbconn->GenId($recostable);

        // SQL to insert the item
        $sql = "INSERT INTO $recostable (xar_rid,xar_uname,xar_iid1,xar_iid2)
                VALUES (?,?,?,?)";
        $bindvars = array($rid, $uname, $iid1, $iid2);
        $result = &$dbconn->Execute($sql, $bindvars);
        if (!$result) return false;
        // close result set
        $result->Close();
      }

    return true;
}
?>
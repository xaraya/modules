<?php
/**
 * crispBB Forum Module
 *
 * @package modules
 * @copyright (C) 2008-2009 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage crispBB Forum Module
 * @link http://xaraya.com/index.php/release/970.html
 * @author crisp <crisp@crispcreations.co.uk>
 *//**
 * Do something
 *
 * Standard function
 *
 * @author crisp <crisp@crispcreations.co.uk>
 * @return array
 * @throws none
 */
function crispbb_userapi_search($args)
{

    if (empty($args) || count($args) < 1) {
        return;
    }

    extract($args);

    if($q == ''){
        return;
    }

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $exampletable = & $xartable['example'];
    $where = '';
    $items = array();
    $sql = "SELECT  xar_exid,
                    xar_name,
                    xar_number
              FROM  $exampletable
              WHERE  (";

    /* setup arrays for the sql where clause, and the bindvars array */
    $bindvars = array();

    /* include search on exid - the item id - if required */
    /*
    if (isset($exid)) {
        $sql .= "xar_exid = ?";
        $bindvars[] = $exid;
    }
    */

    /* if you needed author search with uid
     * you might do something like the following.
     * Don't forget to add to the other checks following as well*/
    /*if (isset($uid)) {
        if (isset($exid)) {
            $sql .= " OR ";
        }
        $sql .= "xar_uid LIKE ? ";
        $bindvars[] = $uid;
    }
    */

    if (isset($name)) {
        $sql .= " xar_name LIKE ?"; /* Item name must match exactly */
        $bindvars[] = $name;
    }
    if (isset($number)) {
        if (isset($name)) {
            $sql .= " OR ";
        }
        $sql .= " xar_number = ?";
        $bindvars[] = $number;
    }
    /* we will sort by the item name here, ascending */
    $sql .=")  ORDER BY xar_name ASC";
    /* Execute the actual query.
     * The execute function combines the $sql query with the $bindvars
     */
    $result =& $dbconn->Execute($sql, $bindvars);

    if (!$result) return;
    /* no results to return .. then return none :p */
    if ($result->EOF) {
        return array();
    }
    /* Loop over the result array
     * We are returning a multidimensional array, even if we return one result
     */
    for (; !$result->EOF; $result->MoveNext()) {
        list($exid, $name, $number) = $result->fields;
        /* don't forget to add your user id uid here if you're searching on author */
        if (xarSecurityCheck('ReadExample', 0)) {
            $items[] = array('exid'   => $exid,
                                'name'   => $name,
                                'number' => $number);
        }
    }
    $result->Close();

    /* Return any results */
    return $items;

}
?>
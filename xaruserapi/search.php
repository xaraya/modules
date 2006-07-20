<?php
/**
 * Search the database for example items
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Example Module
 * @link http://xaraya.com/index.php/release/36.html
 * @author Example Module Development Team
 */
/**
 * Searches all example items
 *
 * This function performes the search in the database. It is set to search
   for items that match any of the parameters in $args.
 *
 * @author jojodee
 * @param string $args['name']
 * @param int    $args['number']
 * @access private
 * @return array mixed description of return
 */
function example_userapi_search($args)
{
    extract($args);

    if (empty($args) || count($args) < 1) {
        return;
    }

     if($q == ''){
        return;
    }

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $exampletable = & $xartable['example'];
    $where = '';
    $releases = array();
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

    $result =& $dbconn->Execute($sql, $bindvars);

    if (!$result) return;
    /* no results to return .. then return none :p */
    if ($result->EOF) {
        return array();
    }
    for (; !$result->EOF; $result->MoveNext()) {
        list($exid, $name, $number) = $result->fields;
        /* don't forget to add your user id uid here if you're searching on author */
        if (xarSecurityCheck('ReadExample', 0)) {
            $example[] = array('exid'   => $exid,
                                'name'   => $name,
                                'number' => $number);
        }
    }
    $result->Close();

    /* Return any results */
    return $example;

}
?>

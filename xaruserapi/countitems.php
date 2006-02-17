<?php
/**
 * Utility function counts number of items held by this module
 *
 * @package modules
 * @copyright (C) 2005-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Sigmapersonnel Module
 * @link http://xaraya.com/index.php/release/418.html
 * @author MichelV.
 */
/**
 * utility function to count the number of personnel items
 *
 * @author MichelV <michelv@xarayahosting.nl>
 * @param int catid
 * @param int persstatus
 * @param enum oncall
 * @returns integer
 * @return number of items held by this module
 * @throws DATABASE_ERROR

 */
function sigmapersonnel_userapi_countitems($args)
{
    $count = count($args);
    extract($args);
    if (!xarVarFetch('catid', 'int:1:', $catid, '',XARVAR_NOT_REQUIRED)) return; // 0 is nothing
    if (!xarVarFetch('persstatus', 'int:1:', $persstatus, '',XARVAR_NOT_REQUIRED)) return;
  //  if (!xarVarFetch('oncall', 'enum:ONCALL:NOTONCALL', $oncall, 'ONCALL', XARVAR_NOT_REQUIRED)) return;
    // Get database setup
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    // It's good practice to name the table and column definitions you are
    // getting - $table and $column don't cut it in more complex modules
    $sigmapersonneltable = $xartable['sigmapersonnel_person'];
    $where = '';
    // Count the items
    $query = "SELECT COUNT(1) ";

    // catid is not empty
    if (!empty($catid) && xarModIsHooked('categories','sigmapersonnel')) {
        // Get the LEFT JOIN ... ON ...  and WHERE parts from categories
        $categoriesdef = xarModAPIFunc('categories','user','leftjoin',
                                       array('modid' => xarModGetIDFromName('sigmapersonnel'),
                                             'catid' => $catid));

        $query .= " FROM ( $sigmapersonneltable
                    LEFT JOIN $categoriesdef[table]
                    ON $categoriesdef[field] = xar_personid )
                    $categoriesdef[more]";
                    if (count($args) == 0) {
                        $query .= " WHERE $categoriesdef[where]";
                    } elseif (count($args)>1) {
                        $query .= " WHERE ($categoriesdef[where]";
                    }

    } else {
        if ($count == 0) {
            $query .= " FROM $sigmapersonneltable
                      ";
        } elseif ($count > 0) {
            $query .= " FROM $sigmapersonneltable WHERE (
                      ";
        }
    }

    $bindvars = array();

    if (!empty($persstatus)) {
        $query .= "xar_persstatus LIKE ?";
        $bindvars[] = $persstatus;
    }

    if (!empty($oncall)) {
        if (!empty($persstatus)) {
            $query .= " OR ";
        }
        $query .= "xar_persstatus = ? ";
        $bindvars[] = $persstatus;
    }
/*
    if (isset($regname)) {
        if (isset($rid) || isset($tid)) {
            $sql .= " OR ";
        }
        $sql .= " xar_regname LIKE ?";

        $bindvars[] = '%'.$regname.'%';
    }
   if (isset($displname)) {
        if (isset($rid) || isset($tid) || isset($regname)) {
            $sql .= " OR ";
        }
        $sql .= " xar_displname LIKE ?";
        $bindvars[] = '%'.$displname.'%';
    }
*/
    if ($count>0) {
        $query .= ") ";
    }



    // If there are no variables you can pass in an empty array for bind variables
    // or no parameter.
    $result = &$dbconn->Execute($query,$bindvars);
    // Check for an error with the database code, adodb has already raised
    // the exception so we just return
    if (!$result) return;
    // Obtain the number of items
    list($numitems) = $result->fields;
    // All successful database queries produce a result set, and that result
    // set should be closed when it has been finished with
    $result->Close();
    // Return the number of items
    return $numitems;
}
?>

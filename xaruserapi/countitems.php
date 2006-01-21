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
 * @author Michel V.
 */
/**
 * utility function to count the number of personnel items
 *
 * @author MichelV <michelv@xarayahosting.nl>
 * @param int catid
 * @param int persstatus
 * @returns integer
 * @return number of items held by this module
 * @raise DATABASE_ERROR
 * @TODO extend for catid and other parameters
 */
function sigmapersonnel_userapi_countitems($args)
{
    extract($args);
    if (!xarVarFetch('catid', 'int:1:', $catid, '',XARVAR_NOT_REQUIRED)) return; // 0 is nothing
    if (!xarVarFetch('persstatus', 'int:1:', $persstatus, '',XARVAR_NOT_REQUIRED)) return;
    // Get database setup
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    // It's good practice to name the table and column definitions you are
    // getting - $table and $column don't cut it in more complex modules
    $sigmapersonneltable = $xartable['sigmapersonnel_person'];
    // Get item - the formatting here is not mandatory, but it does make the
    // SQL statement relatively easy to read.  Also, separating out the sql
    // statement from the Execute() command allows for simpler debug operation
    // if it is ever needed
    $query = "SELECT COUNT(1) ";

    // catid is not empty
    if (!empty($catid) && xarModIsHooked('categories','sigmapersonnel')) {
        // Get the LEFT JOIN ... ON ...  and WHERE parts from categories
        $categoriesdef = xarModAPIFunc('categories','user','leftjoin',
                                       array('modid' => xarModGetIDFromName('sigmapersonnel'),
                                             'catid' => $catid));

        $query .= " FROM ($sigmapersonneltable
                    LEFT JOIN $categoriesdef[table]
                    ON $categoriesdef[field] = xar_personid )
                    $categoriesdef[more]
                    WHERE $categoriesdef[where]";
                    if(!empty($persstatus)) {
                        $query .= " AND ";
                    }
    } else {
        $query .= " FROM $sigmapersonneltable";
    }
    if (!empty($persstatus) && empty($catid)) {
        $query .= " WHERE xar_persstatus = $persstatus";
    } elseif (!empty($persstatus) && !empty($catid)) {
        // AND has been added
        $query .= " xar_persstatus = $persstatus";
    }

    // If there are no variables you can pass in an empty array for bind variables
    // or no parameter.
    $result = &$dbconn->Execute($query,array());
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

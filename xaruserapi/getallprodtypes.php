<?php
/**
 * File: $Id:
 * 
 * Get all productypes
 * File: $Id:
 * @copyright (C) 2004 by Jo Dalle Nogare
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://xaraya.athomeandabout.com
 *
 * @subpackage xarcpshop
 * @author jojodee@xaraya.com
 */

/**
 * get all product types
 *
 * @param numitems $ the number of items to retrieve (default -1 = all)
 * @param startnum $ start with this item number (default 1)
 * @returns array
 * @return array of items, or false on failure
 * @raise BAD_PARAM, DATABASE_ERROR, NO_PERMISSION
 */
function xarcpshop_userapi_getallprodtypes($args)
{
    extract($args);

    if (!isset($startnum)) {
        $startnum = 1;
    }
    if (!isset($numitems)) {
        $numitems = -1;
    }

    $invalid = array();
    if (!isset($startnum) || !is_numeric($startnum)) {
        $invalid[] = 'startnum';
    }
    if (!isset($numitems) || !is_numeric($numitems)) {
        $invalid[] = 'numitems';
    }
    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
            join(', ', $invalid), 'user', 'getall', 'xarCPShop');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
            new SystemException($msg));
        return;
    }

    $items = array();
    // Security check - important to do this as early on as possible to
    // avoid potential security holes or just too much wasted processing
    if (!xarSecurityCheck('ViewxarCPShop')) return;

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    // It's good practice to name the table definitions you are
    // using - $table doesn't cut it in more complex modules
    $cptypestable = $xartable['cptypes'];
    $query = "SELECT xar_prodtypeid,
                   xar_prodtype,
                   xar_description
               FROM $cptypestable
            ORDER BY xar_prodtype";

    $result = $dbconn->SelectLimit($query, $numitems, $startnum-1);

    if (!$result) return;

    for (; !$result->EOF; $result->MoveNext()) {
        list($prodtypeid, $prodtype, $description) = $result->fields;
        if (xarSecurityCheck('ViewxarCPShop', 0, 'Item', "$prodtype:All:$prodtypeid")) {
            $items[] = array('prodtypeid' => $prodtypeid,
                'prodtype' => $prodtype,
                'description' => $description);
        }
    }
    // All successful database queries produce a result set, and that result
    // set should be closed when it has been finished with
    $result->Close(); 
    // Return the items
    return $items;
} 

?>

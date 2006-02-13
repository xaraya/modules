<?php
/**
 * Get a specific master item
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage legis Module
 * @link http://xaraya.com/index.php/release/593.html
 * @author Jo Dalle Nogare <jojodee@xaraya.com>
 */

/**
 * Get a specific item
 * 
 * Standard function oto retrieve a specific document
 *
 * @author jojodee
 * @param  $args ['mdid'] id of legis item to get
 * @returns array
 * @return item array, or false on failure
 * @raise BAD_PARAM, DATABASE_ERROR, NO_PERMISSION
 */
function legis_userapi_getmaster($args)
{
    extract($args);

   if (!isset($mdid) || !is_numeric($mdid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
            'item ID', 'user', 'get', 'legis');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
            new SystemException($msg));
        return;
    }
    $dbconn =& xarDBGetConn();
    $xarTables =& xarDBGetTables();

   $LegisMasterTable = $xarTables['legis_master'];
  $query = "SELECT   xar_mdid,
                   xar_mdname,
                   xar_mdorder,
                   xar_mddef
              FROM $LegisMasterTable
              WHERE xar_mdid = ?";

    $result = &$dbconn->Execute($query,array($mdid));
    /* Check for an error with the database code, adodb has already raised
     * the exception so we just return
     */
    if (!$result) return;
    /* Check for no rows found, and if so, close the result set and return an exception */
    if ($result->EOF) {
        $result->Close();
        $msg = xarML('This item does not exist');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'ID_NOT_EXIST',
            new SystemException(__FILE__ . '(' . __LINE__ . '): ' . $msg));
        return;
    }
    /* Obtain the item information from the result set */
          list($mdid, $mdname, $mdorder, $mddef) = $result->fields;
    $result->Close();
    /* Create the item array */
         $item =array('mdid' => (int)$mdid,
                                  'mdname' => $mdname,
                                  'mdorder' => $mdorder,
                                  'mddef'   => $mddef);
    /* Return the item array */
    return $item;
}
?>

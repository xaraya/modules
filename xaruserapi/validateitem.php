<?php
/**
 * File: $Id:
 * 
 * Validate an item
 * 
 * @copyright (C) 2004 by Jo Dalle Nogare
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://xaraya.athomeandabout.com
 *
 * @subpackage xarcpshop
 * @author jojodee@xaraya.com
 */
function xarcpshop_userapi_validateitem($args)
{ 
      extract($args);
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
  
    $cpstorestable = $xartable['cpstores'];
  
    $query = "SELECT xar_name
              FROM $cpstorestable
              WHERE xar_name= ?";

    $result = &$dbconn->Execute($query,array($name));
    // Check for an error with the database code, adodb has already raised
    // the exception so we just return
    if (!$result) return;
    // Obtain the item information from the result set
    list($name) = $result->fields;
    // All successful database queries produce a result set, and that result
    // set should be closed when it has been finished with
    $result->Close();

    if (!xarSecurityCheck('ReadxarCPShop', 1, 'Item', "$name:All:All")) {
        return;
    }
    // Create the item array
    $item = array('name' => $name);
    // Return the item array
    return $item;
}

?>

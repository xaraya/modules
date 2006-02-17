<?php
/**
 * Validate an item
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Sigmapersonnel Module
 * @author Michel V.
 */
function sigmapersonnel_userapi_validateitem($args)
{
    extract($args);

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $sigmapersonneltable = $xartable['sigmapersonnel_person'];
    // Get item
    $query = "SELECT xar_pnumber
              FROM $sigmapersonneltable
              WHERE xar_pnumber = ?";
    $result = &$dbconn->Execute($query,array($pnumber));
    if (!$result) return;
    // Obtain the item information from the result set
    list($pnumber) = $result->fields;
    $result->Close();
    // Security check
    if (!xarSecurityCheck('ReadSIGMAPersonnel', 1, 'PersonnelItem', "All:All:All")) { // TODO
        return;
    }
    // Create the item array
    $item = array('pnumber' => $pnumber);
    // Return the item array
    return $item;
}

?>

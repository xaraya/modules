<?php
/**
 * Validate an item
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage legis Module
 * @link http://xaraya.com/index.php/release/593.html
 * @author jojodee
 */
 /*
  * Validate an item
  *
  * @author jojodee
  */
function legis_userapi_validateitem($args)
{ 
    extract($args);

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    $legistable = $xartable['legis'];
    $query = "SELECT xar_name
            FROM $legistable
            WHERE xar_name = ?";

    $result = &$dbconn->Execute($query,array($name));
    /* Check for an error with the database code, adodb has already raised
     * the exception so we just return
     */
    if (!$result) return;

    /* Obtain the item information from the result set */
    list($name) = $result->fields;
    /* All successful database queries produce a result set, and that result
     * set should be closed when it has been finished with
     */
    $result->Close();
    /* Security check - important to do this as early on as possible to avoid
     * potential security holes or just too much wasted processing.  Although
     * this one is a bit late in the function it is as early as we can do it as
     * this is the first time we have the relevant information.
     * For this function, the user must *at least* have READ access to this item
     */
    if (!xarSecurityCheck('Readlegis', 1, 'Item', "$name:All:$name")) {
        return;
    }
    /* Create the item array */
    $item = array('name' => $name);
    /* Return the item array */
    return $item;
}
?>

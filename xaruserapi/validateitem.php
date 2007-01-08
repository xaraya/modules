<?php
/**
 * Validate an item
 *
 * @package modules
 * @copyright (C) 2005-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage ITSP Module
 * @link http://xaraya.com/index.php/release/572.html
 * @author ITSP Module Development Team
 */
 /**
  * Validate an item
  *
  * @author the ITSP module development team
  * @todo everything
  */
function itsp_userapi_validateitem($args)
{
    /* Get arguments from argument array - all arguments to this function
     * should be obtained from the $args array, getting them from other places
     * such as the environment is not allowed, as that makes assumptions that
     * will not hold in future versions of Xaraya
     */
    extract($args);
    /* Argument check - make sure that all required arguments are present and
     * in the right format, if not then set an appropriate error message
     * and return
     * Get database setup - note that both xarDBGetConn() and xarDBGetTables()
     * return arrays but we handle them differently.  For xarDBGetConn() we
     * currently just want the first item, which is the official database
     * handle.  For xarDBGetTables() we want to keep the entire tables array
     * together for easy reference later on
     */
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    /* It's good practice to name the table and column definitions you are
     * getting - $table and $column don't cut it in more complex modules
     */
    $itsptable = $xartable['itsp'];
    /* Get item - the formatting here is not mandatory, but it does make the
     * SQL statement relatively easy to read.  Also, separating out the sql
     * statement from the Execute() command allows for simpler debug operation
     * if it is ever needed
     */
    $query = "SELECT xar_name
            FROM $itsptable
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
    if (!xarSecurityCheck('ReadITSP', 1, 'Item', "$name:All")) {
        return;
    }
    /* Create the item array */
    $item = array('name' => $name);
    /* Return the item array */
    return $item;
}
?>
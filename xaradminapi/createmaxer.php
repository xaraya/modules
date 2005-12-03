<?php
/**
 * Create a new maxercalls item
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Maxercalls Module
 * @link http://xaraya.com/index.php/release/36.html
 * @author Maxercalls Module Development Team
 */

/**
 * Create a new maxercalls item
 *
 * This is a standard adminapi function to create a module item
 *
 * @author the Maxercalls module development team
 * @param  $args ['name'] name of the item
 * @param  $args ['number'] number of the item
 * @returns int
 * @return maxercalls item ID on success, false on failure
 * @raise BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
 */
function maxercalls_adminapi_createmaxer($args)
{
    extract($args);
    /* Argument check - make sure that all required arguments are present
     * and in the right format, if not then set an appropriate error
     * message and return
     * Note : since we have several arguments we want to check here, we'll
     * report all those that are invalid at the same time...


    $invalid = array();
    if (!isset($name) || !is_string($name)) {
        $invalid[] = 'name';
    }
    if (!isset($number) || !is_numeric($number)) {
        $invalid[] = 'number';
    }
    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
            join(', ', $invalid), 'admin', 'create', 'Maxercalls');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
            new SystemException($msg));
        return;
    }
     */    /* Security check - important to do this as early on as possible to
     * avoid potential security holes or just too much wasted processing
     */
    if (!xarSecurityCheck('DeleteMaxercalls')) {
        return;
    }
    /* Get database setup - note that both xarDBGetConn() and xarDBGetTables()
     * return arrays but we handle them differently. For xarDBGetConn()
     * we currently just want the first item, which is the official
     * database handle. For xarDBGetTables() we want to keep the entire
     * tables array together for easy reference later on
     */
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    /* It's good practice to name the table and column definitions you
     * are getting - $table and $column don't cut it in more complex
     * modules
     */
    $maxerstable = $xartable['maxercalls_maxers'];
    /* Get next ID in table - this is required prior to any insert that
     * uses a unique ID, and ensures that the ID generation is carried
     * out in a database-portable fashion
     */
    $nextId = $dbconn->GenId($maxerstable);

    $query = "INSERT INTO $maxerstable (
                           xar_maxerid,
                           xar_personid,
                           xar_ric,
                           xar_maxernumber,
                           xar_function,
                           xar_program,
                           xar_maxerstatus,
                           xar_remark)
                        VALUES (?,?,?,?,?,?,?,?)";
    $bindvars = array($nextId, $personid,$ric, $maxernumber,$function,$program,$maxerstatus,$remark);
    $result = &$dbconn->Execute($query,$bindvars);

    /* Check for an error with the database code, adodb has already raised
     * the exception so we just return
     */
    if (!$result) return;

    /* Get the ID of the item that we inserted. It is possible, depending
     * on your database, that this is different from $nextId as obtained
     * above, so it is better to be safe than sorry in this situation
     */
    $maxerid = $dbconn->PO_Insert_ID($maxercallstable, 'xar_maxerid');

    /* Let any hooks know that we have created a new item.
     * TODO: evaluate
     * xarModCallHooks('item', 'create', $exid, 'exid');
     */
    $item = $args;
    $item['module'] = 'maxercalls';
    $item['itemid'] = $maxerid;
    xarModCallHooks('item', 'create', $maxerid, $item);
    /* Return the id of the newly created item to the calling process */
    return $maxerid;
}
?>
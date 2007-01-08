<?php
/**
 * Create a new maxer
 *
 * @package modules
 * @copyright (C) 2005-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Maxercalls Module
 * @link http://xaraya.com/index.php/release/247.html
 * @author Maxercalls Module Development Team
 */
/**
 * Create a new maxer
 *
 * This is a standard adminapi function to create a module item
 *
 * @author the Maxercalls module development team
 * @param int $args ['ric'] unique number of the pager
 * @param int $args ['personid'] number of person carrying this pager
 * @param int $args ['status']
 * @param string $args['remark'] Additional info on maxer
 * @return int maxercalls item ID on success, false on failure
 * @throws BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
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
     */
    /* Security check - important to do this as early on as possible to
     * avoid potential security holes or just too much wasted processing
     */
    if (!xarSecurityCheck('AddMaxercalls',1,'Maxer')) {
        return;
    }
    /* Get database setup - note that both xarDBGetConn() and xarDBGetTables()
     */
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
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

    /* Get the ID of the item that we inserted.
     */
    $maxerid = $dbconn->PO_Insert_ID($maxerstable, 'xar_maxerid');

    /* Let any hooks know that we have created a new item.
     */
    $item = $args;
    $item['module'] = 'maxercalls';
    $item['itemid'] = $maxerid;
    $item['itemtype'] = 2;
    xarModCallHooks('item', 'create', $maxerid, $item);
    /* Return the id of the newly created item to the calling process */
    return $maxerid;
}
?>
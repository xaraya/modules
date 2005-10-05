<?php
/**
 * @copyright (C) 2004 by Jo Dalle Nogare
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://xaraya.athomeandabout.com
 *
 * @subpackage xarcpshop
 * @author jojodee@xaraya.com
 */
/**
 * create a new shop item
 *
 * @param  $args ['name'] name of the shop
 * @param  $args ['sid'] shop id
 * @return xarcpshop item ID on success, false on failure
 * @raise BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
 */
function xarcpshop_adminapi_create($args)
{
      extract($args);

    $invalid = array();
    if (!isset($name) || !is_string($name)) {
        $invalid[] = 'name';
    }
    if (!isset($nickname) || !is_string($nickname)) {
        $invalid[] = 'nickname';
    }
   if (!isset($toplevel) || !is_string($toplevel)) {
        $toplevel='';
    }
   if (!isset($tid) || !is_numeric($tid)) {
        $tid=0;
    }
    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
            join(', ', $invalid), 'admin', 'create', 'xarCPShop');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
            new SystemException($msg));
        return;
    }
    // Security check - important to do this as early on as possible to
    // avoid potential security holes or just too much wasted processing
    if (!xarSecurityCheck('AddxarCPShop', 1, 'Item', "$name:All:All")) {
        return;
    }

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    // It's good practice to name the table and column definitions you
    // are getting - $table and $column don't cut it in more complex
    // modules
    $cpstorestable = $xartable['cpstores'];

    $nextId = $dbconn->GenId($cpstorestable);

    $query = "INSERT INTO $cpstorestable (
              xar_storeid,
              xar_name,
              xar_nickname,
              xar_toplevel,
              xar_tid)
            VALUES (?,?,?,?,?)";

    $bindvars = array($nextId, (string)$name, (string)$nickname, (string)$toplevel, $tid);
    $result = &$dbconn->Execute($query,$bindvars);

    // Check for an error with the database code, adodb has already raised
    // the exception so we just return
    if (!$result) return;
    // Get the ID of the item that we inserted.  It is possible, depending
    // on your database, that this is different from $nextId as obtained
    // above, so it is better to be safe than sorry in this situation
    $storeid = $dbconn->PO_Insert_ID($cpstorestable, 'xar_storeid');
    $item = $args;
    $item['module'] = 'xarcpshop';
    $item['itemid'] = $storeid;
    xarModCallHooks('item', 'create', $storeid, $item);
    // Return the id of the newly created item to the calling process
    return $storeid;
}

?>
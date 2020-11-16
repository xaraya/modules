<?php
/**
 * Comments Module
 *
 * @package modules
 * @subpackage comments
 * @category Third Party Xaraya Module
 * @version 2.4.0
 * @copyright see the html/credits.html file in this release
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://xaraya.com/index.php/release/14.html
 * @author Carl P. Corliss <rabbitt@xaraya.com>
 */
/**
 * Get the number of comments for a module item
 *
 * @author mikespub
 * @access public
 * @param integer    $modid     the id of the module that these nodes belong to
 * @param integer    $itemtype  the item type that these nodes belong to
 * @param integer    $objectid    the id of the item that these nodes belong to
 * @param integer    $status    the status of the comment: 2 - active, 1 - inactive, 3 - root node
 * @returns integer  the number of comments for the particular modid/objectid pair,
 *                   or raise an exception and return false.
 */
function comments_userapi_get_count($args)
{
    extract($args);

    $exception = false;

    if (!isset($modid) || empty($modid)) {
        $msg = xarML(
            'Invalid #(1) for #(2) function #(3)() in module #(4)',
            'modid',
            'userapi',
            'get_count',
            'comments'
        );
        throw new BadParameterException($msg);
    }

    if (!isset($status) || !is_numeric($status)) {
        $status = _COM_STATUS_ON;
    }

    if (!isset($objectid) || empty($objectid)) {
        $msg = xarML(
            'Invalid #(1) for #(2) function #(3)() in module #(4)',
            'objectid',
            'userapi',
            'get_count',
            'comments'
        );
        throw new BadParameterException($msg);
    }

    $dbconn = xarDB::getConn();
    $xartable =& xarDB::getTables();

    $sql = "SELECT  COUNT(id) as numitems
              FROM  $xartable[comments]
             WHERE  objectid = ? AND modid = ?
               AND  status = ?";
    // Note: objectid is not an integer here (yet ?)
    $bindvars = array((string) $objectid, (int) $modid, (int) $status);

    if (isset($itemtype) && is_numeric($itemtype)) {
        $sql .= " AND itemtype = ?";
        $bindvars[] = (int) $itemtype;
    }

    $result =& $dbconn->Execute($sql, $bindvars);
    if (!$result) {
        return;
    }

    if ($result->EOF) {
        return 0;
    }

    list($numitems) = $result->fields;

    $result->Close();

    return $numitems;
}

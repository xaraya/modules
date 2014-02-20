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
 * Delete all comments attached to the specified objectid / modid pair
 *
 * @author Carl P. Corliss (aka rabbitt)
 * @access  private
 * @param   integer     $modid      the id of the module that the comments are associated with
 * @param   integer     $modid      the item type that the comments are associated with
 * @param   integer     $objectid   the id of the object within the specified module that the comments are attached to
 * @returns bool true on success, false otherwise
 */
function comments_adminapi_delete_object_nodes( $args )
{
    extract($args);

    if (empty($objectid)) {
        $msg = xarML('Missing or Invalid parameter \'objectid\'!!');
        throw new BadParameterException($msg);
    }

    if (empty($modid)) {
        $msg = xarML('Missing or Invalid parameter \'modid\'!!');
        throw new BadParameterException($msg);
    }

    if (!isset($itemtype)) {
        $itemtype = 0;
    }

    $dbconn = xarDB::getConn();
    $xartable =& xarDB::getTables();

    $sql = "DELETE
              FROM  $xartable[comments]
             WHERE  modid    = $modid
               AND  itemtype = $itemtype
               AND  objectid = '$objectid'";

    $result =& $dbconn->Execute($sql);

    if (!isset($result)) {
        return;
    } elseif (!$dbconn->Affected_Rows()) {
        return FALSE;
    } else {
        return TRUE;
    }
}
?>
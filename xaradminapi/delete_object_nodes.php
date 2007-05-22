<?php
/**
 * Comments module - Allows users to post comments on items
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Comments Module
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
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return;
    }

    if (empty($modid)) {
        $msg = xarML('Missing or Invalid parameter \'modid\'!!');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return;
    }

    if (!isset($itemtype)) {
        $itemtype = 0;
    }

    $dbconn = xarDB::getConn();
    $xartable = xarDB::getTables();

    $ctable = &$xartable['comments_column'];

    $sql = "DELETE
              FROM  $xartable[comments]
             WHERE  $ctable[modid]    = $modid
               AND  $ctable[itemtype] = $itemtype
               AND  $ctable[objectid] = '$objectid'";

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
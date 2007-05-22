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
 * Get the number of comments for a module item
 *
 * @author mikespub
 * @access public
 * @param integer    $modid     the id of the module that these nodes belong to
 * @param integer    $itemtype  the item type that these nodes belong to
 * @param integer    $objectid    the id of the item that these nodes belong to
 * @returns integer  the number of comments for the particular modid/objectid pair,
 *                   or raise an exception and return false.
 */
function comments_userapi_get_count($args)
{
    extract($args);

    $exception = false;

    if ( !isset($modid) || empty($modid) ) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                                 'modid', 'userapi', 'get_count', 'comments');
        xarErrorSet(XAR_USER_EXCEPTION, 'BAD_PARAM',
                        new SystemException($msg));
        $exception |= true;
    }


    if ( !isset($objectid) || empty($objectid) ) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                                 'objectid', 'userapi', 'get_count', 'comments');
        xarErrorSet(XAR_USER_EXCEPTION, 'BAD_PARAM',
                        new SystemException($msg));
        $exception |= true;
    }

    if ($exception) {
        return;
    }

    $dbconn = xarDB::getConn();
    $xartable = xarDB::getTables();
    $ctable = &$xartable['comments_column'];

    $sql = "SELECT  COUNT($ctable[id]) as numitems
              FROM  $xartable[comments]
             WHERE  $ctable[objectid]=? AND $ctable[modid]=?
               AND  $ctable[status]=?";
// Note: objectid is not an integer here (yet ?)
    $bindvars = array((string) $objectid, (int) $modid, (int) _COM_STATUS_ON);

    if (isset($itemtype) && is_numeric($itemtype)) {
        $sql .= " AND $ctable[itemtype]=?";
        $bindvars[] = (int) $itemtype;
    }

    $result =& $dbconn->Execute($sql,$bindvars);
    if (!$result)
        return;

    if ($result->EOF) {
        return 0;
    }

    list($numitems) = $result->fields;

    $result->Close();

    return $numitems;
}

?>

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
 * Activate the specified comment
 *
 * @author   Carl P. Corliss (aka rabbitt)
 * @access   public
 * @param    integer     $cid     id of the comment to lookup
 * @returns  bool        returns true on success, throws an exception and returns false otherwise
 */
function comments_userapi_activate( $args )
{

    extract($args);

    if (empty($cid)) {
        $msg = xarML('Missing or Invalid parameter \'cid\'!!');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return;
    }

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    // First grab the objectid and the modid so we can
    // then find the root node.
    $sql = "UPDATE $xartable[comments]
            SET xar_status='" . _COM_STATUS_ON."'
            WHERE xar_cid=?";
    $bindvars = array((int) $cid);

    $result =& $dbconn->Execute($sql,$bindvars);

    if (!$result)
        return;
}


?>

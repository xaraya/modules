<?php
/**
 * Comments module - Allows users to post comments on items
 *
 * @package modules
 * @copyright (C) 2002-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage comments
 * @link http://xaraya.com/index.php/release/14.html
 * @author Johnny Robeson <johnny@localmomentum.net>
 */


/**
 * Set a specified status for a comment and it's children (optional)
 *
 * @author Johnny Robeson (johnny@localmomentum.net)
 * @access public
 * @param  int     $cid     id of the comment to lookup
 * @param  int     $status  the status to see (@see xarincludes/defines.php)
 * @param  bool    $children whether status should be set on the comment children as well
 * @return bool        returns true on success, throws an exception and returns false otherwise
 * @todo   implement hidden/off comment status/support
 */
function comments_userapi_setstatus($args)
{
    if (!xarSecurityCheck('Comments-Moderator')) return;

    extract($args);

    if (empty($cid)) {
        $msg = xarML("Missing or Invalid parameter 'cid'");
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return;
    }

    if (empty($status)) {
        $msg = xarML("Missing or Invalid parameter 'status'");
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return;
    }
    if (!isset($children)) $children = false;

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    $sql = "UPDATE $xartable[comments]
            SET xar_status=?
            WHERE xar_cid=?";
    $bindvars = array((int)$status,(int) $cid);

    $result =& $dbconn->Execute($sql,$bindvars);
    if (!$result) return;

    if ($children) {
        $lnr = xarModAPIFunc('comments', 'user', 'get_node_lrvalues',
                       array('cid' => $cid));

        $childrenlist = xarModAPIFunc('comments','user','get_childcountlist',
                                array('modid' => $lnr['xar_modid'],
                                      'itemtype' => $lnr['xar_itemtype'],
                                      'objectid' => $lnr['xar_objectid'],
                                      'left' => $lnr['xar_left'],
                                      'right' => $lnr['xar_right']));

        $list = join(',',array_keys($childrenlist));
        $sql = "UPDATE xar_comments SET xar_status=? WHERE xar_cid IN($list)";

        $result =& $dbconn->Execute($sql, array($status));
        if (!$result) return;

        if ($result->EOF) return array();

    }
    return true;
}
?>

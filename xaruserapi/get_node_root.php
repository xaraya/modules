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
 * Grab the id, left and right values for the
 * root node of a particular comment.
 *
 * @author   Carl P. Corliss (aka rabbitt)
 * @access   public
 * @param    integer     modid      The module that comment is attached to
 * @param    integer     objectid   The particular object within that module
 * @param    integer     itemtype   The itemtype of that object
 * @returns  array an array containing the left and right values or an
 *                 empty array if the comment_id specified doesn't exist
 */
function comments_userapi_get_node_root( $args )
{

    extract ($args);

    $exception = false;

    if (!isset($modid) || empty($modid)) {
        $msg = xarML('Missing or Invalid parameter \'modid\'!!');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        $exception |= true;
    }

    if (!isset($objectid) || empty($objectid)) {
        $msg = xarML('Missing or Invalid parameter \'objectid\'!!');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        $exception |= true;
    }

    if ($exception) {
        return;
    }

    if (empty($itemtype)) {
        $itemtype = 0;
    }

    $dbconn = xarDB::getConn();
    $xartable = xarDB::getTables();

    $ctable = &$xartable['comments_column'];
    // grab the root node's id, left and right values
    // based on the objectid/modid pair
    $sql = "SELECT  $ctable[id], $ctable[left], $ctable[right]
              FROM  $xartable[comments]
             WHERE  $ctable[modid]=?
               AND  $ctable[itemtype]=?
               AND  $ctable[objectid]=?
               AND  $ctable[status]=?";
    // objectid is still a string for now
    $bindvars = array((int) $modid, (int) $itemtype, (string) $objectid, (int) _COM_STATUS_ROOT_NODE);

    $result =& $dbconn->Execute($sql,$bindvars);

    if(!$result)
        return;

    $count=$result->RecordCount();

    assert($count==1 | $count==0);

    if (!$result->EOF) {
        $node = $result->GetRowAssoc(false);
    } else {
        $node = array();
    }
    $result->Close();

    return $node;
}

?>

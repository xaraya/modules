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
 * Grab the highest 'right' value for the specified modid/objectid pair
 *
 * @author   Carl P. Corliss (aka rabbitt)
 * @access   private
 * @param    integer     modid      The module that comment is attached to
 * @param    integer     objectid   The particular object within that module
 * @param    integer     itemtype   The itemtype of that object
 * @returns   integer   the highest 'right' value for the specified modid/objectid pair or zero if it couldn't find one
 */
function comments_userapi_get_object_maxright( $args )
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

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $ctable = &$xartable['comments_column'];

    // grab the root node's id, left and right values
    // based on the objectid/modid pair
    $sql = "SELECT  MAX($ctable[right]) as max_right
              FROM  $xartable[comments]
             WHERE  $ctable[objectid] = '$objectid'
               AND  $ctable[itemtype] = $itemtype
               AND  $ctable[modid] = $modid";

    $result =& $dbconn->Execute($sql);

    if (!$result)
        return;

    if (!$result->EOF) {
        $node = $result->GetRowAssoc(false);
    } else {
        $node['max_right'] = 0;
    }
    $result->Close();

    return $node['max_right'];
}

?>

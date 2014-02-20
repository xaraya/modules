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
        throw new BadParameterException($msg);
        $exception |= true;
    }

    if (!isset($objectid) || empty($objectid)) {
        $msg = xarML('Missing or Invalid parameter \'objectid\'!!');
        throw new BadParameterException($msg);
        $exception |= true;
    }

    if ($exception) {
        return;
    }

    if (empty($itemtype)) {
        $itemtype = 0;
    }

    $dbconn = xarDB::getConn();
    $xartable =& xarDB::getTables();

    // grab the root node's id, left and right values
    // based on the objectid/modid pair
    $sql = "SELECT  MAX(right_id) as max_right
              FROM  $xartable[comments]
             WHERE  objectid = '$objectid'
               AND  itemtype = $itemtype
               AND  modid = $modid";

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

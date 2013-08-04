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
 * Acquire a list of objectid's associated with a
 * particular Module ID in the comments table
 *
 * @author Carl P. Corliss (aka rabbitt)
 * @access  private
 * @param   integer     $modid      the id of the module that the objectids are associated with
 * @param   integer     $itemtype   the item type that these nodes belong to
 * @returns array       A list of objectid's
 */
function comments_userapi_get_object_list( $args )
{
    extract($args);

    if (!isset($modid) || empty($modid)) {
        $msg = xarML('Missing #(1) for #(2) function #(3)() in module #(4)',
                                'modid', 'userapi', 'get_object_list', 'comments');
        throw new BadParameterException($msg);
    }

    $dbconn = xarDB::getConn();
    $xartable = xarDB::getTables();
    $sql     = "SELECT DISTINCT objectid AS pageid
                           FROM $xartable[comments]
                          WHERE modid = $modid";

    if (isset($itemtype) && is_numeric($itemtype)) {
        $sql .= " AND itemtype=$itemtype";
    }

    $result =& $dbconn->Execute($sql);
    if (!$result) return;

    // if it's an empty set, return array()
    if ($result->EOF) {
        return array();
    }

    // zip through the list of results and
    // create the return array
    while (!$result->EOF) {
        $row = $result->GetRowAssoc(false);
        $ret[$row['pageid']]['pageid'] = $row['pageid'];
        $result->MoveNext();
    }
    $result->Close();

    return $ret;

}

?>

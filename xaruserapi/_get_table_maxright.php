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
 * Grab the highest 'right' value for the whole comments table
 *
 * Note: this is no longer relevant, since each (modid + itemtype + objectid)
 *       has its own Celko tree
 *
 * @author   Carl P. Corliss (aka rabbitt)
 * @access   private
 * @returns   integer   the highest 'right' value for the table or zero if it couldn't find one
 */
function comments_userapi_get_table_maxright(/* VOID */)
{

    $dbconn = xarDB::getConn();
    $xartable =& xarDB::getTables();


    // grab the root node's id, left and right values
    // based on the objectid/modid pair
    $sql = "SELECT  MAX(right) as max_right
              FROM  $xartable[comments]";

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

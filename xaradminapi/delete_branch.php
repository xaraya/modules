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
 * Delete a branch from the tree
 *
 * @author Carl P. Corliss (aka rabbitt)
 * @access  private
 * @param   integer     $node   the id of the node to delete
 * @returns bool true on success, false otherwise
 */
function comments_adminapi_delete_branch( $args )
{

    extract($args);

    if (empty($node)) {
        $msg = xarML('Invalid or Missing Parameter \'node\'!!');
        throw new BadParameterException($msg);
    }

    // Grab the deletion node's left and right values
    $comments = xarMod::apiFunc('comments','user','get_one',
                              array('id' => $node));
    $left = $comments[0]['left_id'];
    $right = $comments[0]['right_id'];
    $modid = $comments[0]['modid'];
    $itemtype = $comments[0]['itemtype'];
    $objectid = $comments[0]['objectid'];

    $dbconn = xarDB::getConn();
    $xartable = xarDB::getTables();

    $sql = "DELETE
              FROM  $xartable[comments]
             WHERE  left_id    >= $left
               AND  right_id   <= $right
               AND  modid    = $modid
               AND  itemtype = $itemtype
               AND  objectid = '$objectid'";

    $result =& $dbconn->Execute($sql);

    if (!$dbconn->Affected_Rows()) {
        return FALSE;
    }

    // figure out the adjustment value for realigning the left and right
    // values of all the comments
    $adjust_value = (($right - $left) + 1);


    // Go through and fix all the l/r values for the comments
    if (xarMod::apiFunc('comments','user','remove_gap', array('startpoint' => $left,
                                                            'modid'      => $modid,
                                                            'objectid'   => $objectid,
                                                            'itemtype'   => $itemtype,
                                                            'gapsize'    => $adjust_value)))
    {
        return $dbconn->Affected_Rows();
    }

}
?>
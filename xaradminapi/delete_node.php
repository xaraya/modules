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
 * Delete a node from the tree and reassign it's children to it's parent
 *
 * @author Carl P. Corliss (aka rabbitt)
 * @access  private
 * @param   integer     $node   the id of the node to delete
 * @param   integer     $parent_id    the deletion node's parent id (used to reassign the children)
 * @returns bool true on success, false otherwise
 */
function comments_adminapi_delete_node( $args )
{

    extract($args);

    if (empty($node)) {
        $msg = xarML('Missing or Invalid comment id!');
        throw new BadParameterException($msg);
    }

    if (empty($parent_id)) {
        $msg = xarML('Missing or Invalid parent id!');
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

    // Call delete hooks for categories, hitcount etc.
    $args['module'] = 'comments';
    $args['itemtype'] = $itemtype;
    $args['itemid'] = $node;
    xarModHooks::call('item', 'delete', $node, $args);

    //Now delete the item ....
    $dbconn = xarDB::getConn();
    $xartable =& xarDB::getTables();

    // delete the node
    $sql = "DELETE
              FROM  $xartable[comments]
             WHERE  id = ?";
             $bindvars1 = array($node);
    // reset all parent id's == deletion node's id to that of
    // the deletion node's parent id
    $sql2 = "UPDATE $xartable[comments]
                SET parent_id = ?
              WHERE parent_id = ?";
              $bindvars2 = array($parent_id, $node);
    if (!$dbconn->Execute($sql,$bindvars1))
        return;

    if (!$dbconn->Execute($sql2,$bindvars2))
        return;

    // Go through and fix all the l/r values for the comments
    // First we subtract 1 from all the deletion node's children's left and right values
    // and then we subtract 2 from all the nodes > the deletion node's right value
    // and <= the max right value for the table
    if ($right > $left + 1) {
        xarMod::apiFunc('comments','user','remove_gap',array('startpoint' => $left,
                                                           'endpoint'   => $right,
                                                           'modid'      => $modid,
                                                           'objectid'   => $objectid,
                                                           'itemtype'   => $itemtype,
                                                           'gapsize'    => 1));
    }
    xarMod::apiFunc('comments','user','remove_gap',array('startpoint' => $right,
                                                       'modid'      => $modid,
                                                       'objectid'   => $objectid,
                                                       'itemtype'   => $itemtype,
                                                       'gapsize'    => 2));



    return $dbconn->Affected_Rows();
}
?>

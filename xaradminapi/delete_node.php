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
 * Delete a node from the tree and reassign it's children to it's parent
 *
 * @author Carl P. Corliss (aka rabbitt)
 * @access  private
 * @param   integer     $node   the id of the node to delete
 * @param   integer     $pid    the deletion node's parent id (used to reassign the children)
 * @returns bool true on success, false otherwise
 */
function comments_adminapi_delete_node( $args )
{

    extract($args);

    if (empty($node)) {
        $msg = xarML('Missing or Invalid comment id!');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return;
    }

    if (empty($pid)) {
        $msg = xarML('Missing or Invalid parent id!');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return;
    }

    // Grab the deletion node's left and right values
    $comments = xarModAPIFunc('comments','user','get_one',
                              array('id' => $node));
    $left = $comments[0]['cleft'];
    $right = $comments[0]['cright'];
    $modid = $comments[0]['modid'];
    $itemtype = $comments[0]['itemtype'];
    $objectid = $comments[0]['objectid'];

    // Call delete hooks for categories, hitcount etc.
    $args['module'] = 'comments';
    $args['itemtype'] = $itemtype;
    $args['itemid'] = $node;
    xarModCallHooks('item', 'delete', $node, $args);

    //Now delete the item ....
    $dbconn = xarDB::getConn();
    $xartable = xarDB::getTables();

    $ctable = &$xartable['comments_column'];

    // delete the node
    $sql = "DELETE
              FROM  $xartable[comments]
             WHERE  id = ?";
             $bindvars1 = array($node);
    // reset all parent id's == deletion node's id to that of
    // the deletion node's parent id
    $sql2 = "UPDATE $xartable[comments]
                SET pid = ?
              WHERE pid = ?";
              $bindvars2 = array($pid, $node);
    if (!$dbconn->Execute($sql,$bindvars1))
        return;

    if (!$dbconn->Execute($sql2,$bindvars2))
        return;

    // Go through and fix all the l/r values for the comments
    // First we subtract 1 from all the deletion node's children's left and right values
    // and then we subtract 2 from all the nodes > the deletion node's right value
    // and <= the max right value for the table
    if ($right > $left + 1) {
        xarModAPIFunc('comments','user','remove_gap',array('startpoint' => $left,
                                                           'endpoint'   => $right,
                                                           'modid'      => $modid,
                                                           'objectid'   => $objectid,
                                                           'itemtype'   => $itemtype,
                                                           'gapsize'    => 1));
    }
    xarModAPIFunc('comments','user','remove_gap',array('startpoint' => $right,
                                                       'modid'      => $modid,
                                                       'objectid'   => $objectid,
                                                       'itemtype'   => $itemtype,
                                                       'gapsize'    => 2));



    return $dbconn->Affected_Rows();
}
?>

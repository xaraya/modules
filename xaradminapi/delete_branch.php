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

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    $ctable = &$xartable['comments_column'];

    $sql = "DELETE
              FROM  $xartable[comments]
             WHERE  $ctable[left]    >= $left
               AND  $ctable[right]   <= $right
               AND  $ctable[modid]    = $modid
               AND  $ctable[itemtype] = $itemtype
               AND  $ctable[objectid] = '$objectid'";

    $result =& $dbconn->Execute($sql);

    if (!$dbconn->Affected_Rows()) {
        return FALSE;
    }

    // figure out the adjustment value for realigning the left and right
    // values of all the comments
    $adjust_value = (($right - $left) + 1);


    // Go through and fix all the l/r values for the comments
    if (xarModAPIFunc('comments','user','remove_gap', array('startpoint' => $left,
                                                            'modid'      => $modid,
                                                            'objectid'   => $objectid,
                                                            'itemtype'   => $itemtype,
                                                            'gapsize'    => $adjust_value)))
    {
        return $dbconn->Affected_Rows();
    }

}
?>
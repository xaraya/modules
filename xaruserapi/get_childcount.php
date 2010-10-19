<?php
/**
 * Comments module - Allows users to post comments on items
 *
 * @package modules
 * @copyright (C) 2002-2007 The copyright-placeholder
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage comments
 * @link http://xaraya.com/index.php/release/14.html
 * @author Carl P. Corliss <rabbitt@xaraya.com>
 */
/**
 * Get the number of children comments for a particular comment id
 *
 * @author mikespub
 * @access public
 * @param integer    $id       the comment id that we want to get a count of children for
 * @returns integer  the number of child comments for the particular comment id,
 *                   or raise an exception and return false.
 */
function comments_userapi_get_childcount($id)
{

    if ( !isset($id) || empty($id) ) {
        $msg = xarML('Invalid #(1) [#(2)] for #(3) function #(4)() in module #(5)',
                                 'id', $id, 'userapi', 'get_childcount', 'comments');
        throw new BadParameterException($msg);
    }


    $dbconn = xarDB::getConn();
    $xartable = xarDB::getTables();

    $nodelr = xarMod::apiFunc('comments',
                            'user',
                            'get_node_lrvalues',
                             array('id' => $id));

    $sql = "SELECT  COUNT(id) as numitems
              FROM  $xartable[comments]
             WHERE  status="._COM_STATUS_ON."
               AND  (left_id >= $nodelr[left_id] AND right_id <= $nodelr[right_id]";

    $result =& $dbconn->Execute($sql);
    if (!$result)
        return;

    if ($result->EOF) {
        return 0;
    }

    list($numitems) = $result->fields;

    $result->Close();

    // return total count - 1 ... the -1 is so we don't count the comment root.
    return ($numitems - 1);
}

?>

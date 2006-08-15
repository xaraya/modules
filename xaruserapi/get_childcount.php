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
 * Get the number of children comments for a particular comment id
 *
 * @author mikespub
 * @access public
 * @param integer    $cid       the comment id that we want to get a count of children for
 * @returns integer  the number of child comments for the particular comment id,
 *                   or raise an exception and return false.
 */
function comments_userapi_get_childcount($cid)
{

    if ( !isset($cid) || empty($cid) ) {
        $msg = xarML('Invalid #(1) [#(2)] for #(3) function #(4)() in module #(5)',
                                 'cid', $cid, 'userapi', 'get_childcount', 'comments');
        xarErrorSet(XAR_USER_EXCEPTION, 'BAD_PARAM',
                        new SystemException($msg));
        return false;
    }


    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $ctable = &$xartable['comments_column'];

    $nodelr = xarModAPIFunc('comments',
                            'user',
                            'get_node_lrvalues',
                             array('id' => $cid));

    $sql = "SELECT  COUNT($ctable[cid]) as numitems
              FROM  $xartable[comments]
             WHERE  $ctable[status]="._COM_STATUS_ON."
               AND  ($ctable[left] >= $nodelr[xar_left] AND $ctable[right] <= $nodelr[xar_right])";

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

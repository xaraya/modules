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
 * Creates a root node for the specified objectid/modid
 *
 * @author   Carl P. Corliss (aka rabbitt)
 * @access   private
 * @param    integer     modid      The module that comment is attached to
 * @param    integer     objectid   The particular object within that module
 * @param    integer     itemtype   The itemtype of that object
 * @returns  integer     the id of the node that was created so it can be used as a parent id
 * @todo get rid of this notion of root node ?
 */
function comments_userapi_add_rootnode( $args )
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

    $commenttable = $xartable['comments'];

    // Each (modid + itemtype + objectid) has its own Celko tree now,
    // so we start over from 0 for the left and right positions
    $maxright = 0;

    // Set left and right values;
    $left  = $maxright + 1;
    $right = $maxright + 2;
    $cdate = time();

    // Get next ID in table.  For databases like MySQL, this value will
    // be zero but the auto_increment type on the column will create
    // the correct value.
    $nextId = $dbconn->GenId($commenttable);

    $sql = "INSERT INTO $xartable[comments]
              (xar_cid, xar_pid, xar_text,
               xar_title, xar_author, xar_left,
               xar_right, xar_status, xar_objectid,
               xar_modid, xar_itemtype,
               xar_hostname, xar_date )
            VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)";
    $bindvars = array( $nextId,
                       0,
                       'This is for internal use and works only as a place holder. PLEASE do NOT delete this comment as it could have detrimental effects on the consistency of the comments table.',
                       'ROOT NODE - PLACEHOLDER. DO NOT DELETE!',
                       1,
                       $left,
                       $right,
                       _COM_STATUS_ROOT_NODE,
                       $objectid,
                       $modid,
                       $itemtype,
                       '',
                       $cdate
                       );

    $result =& $dbconn->Execute($sql,$bindvars);

    if(!$result)
        return;

    // Return the cid of the created record just now.
    $id = $dbconn->PO_Insert_ID($xartable['comments'], 'xar_cid');

    return $id;
}

?>

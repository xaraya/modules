<?php
/**
 * Create a doc
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Release Module
 * @link http://xaraya.com/index.php/release/773.html
 */
/**
 * Create a doc by user
 * 
 * Original Author of file: John Cox via phpMailer Team
 * @author Release module development team
 * @param rid, title, doc, type, approved
 */
function release_userapi_createdoc($args)
{
    // Get arguments
    extract($args);

    // Argument check
    if ((!isset($eid)) ||
        (!isset($rid)) ||    
        (!isset($title)) ||
        (!isset($doc)) ||
        (!isset($exttype)) ||
        (!isset($approved))) {

        $msg = xarML('Wrong arguments to release_userapi_createdoc.');
        xarErrorSet(XAR_SYSTEM_EXCEPTION,
                        'BAD_PARAM',
                        new SystemException($msg));
        return false;
    }

    // Get datbase setup
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    $releasetable = $xartable['release_docs'];

    if (empty($approved)){
        $approved = 1;
    }

    // Get next ID in table
    $nextId = $dbconn->GenId($releasetable);
    $time = time();
    $query = "INSERT INTO $releasetable (
              xar_rdid,
              xar_eid,
              xar_rid,
              xar_title,
              xar_docs,
              xar_exttype,
              xar_time,
              xar_approved
              )
            VALUES (?,?,?,?,?,?,?,?)";

    $bindvars = array($nextId,$eid,$rid,$title,$doc,$exttype,$time,$approved);
    $result =& $dbconn->Execute($query,$bindvars);
    if (!$result) return;

    // Get the ID of the item that we inserted
    $rdid = $dbconn->PO_Insert_ID($releasetable, 'xar_rdid');

    // Let any hooks know that we have created a new user.
    xarModCallHooks('item', 'create', $rdid, 'rdid');

    // Return the id of the newly created user to the calling process
    return $rdid;

}

?>
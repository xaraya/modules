<?php
/*
 * Censor Module
 *
 * @package modules
 * @copyright (C) 2003 by the Xaraya Development Team
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.com
 * @subpackage  Censor Module
 * @author John Cox
*/

/**
 * create a new censored word
 *
 * @param  $args ['keyword'] new censored word
 * @returns int
 * @return censored word ID on success, false on failure
 */
function censor_adminapi_create($args)
{
    // Get arguments from argument array
    extract($args);
    // Argument check - make sure that all required arguments are present,
    // if not then set an appropriate error message and return
    if (!isset($keyword)) {
        $msg = xarML('Invalid Parameter Count in #(3)_#(1)_#(2).php', 'admin', 'create', 'censor');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return;
    }


    // Security Check
    if (!xarSecurityCheck('AddCensor')) return;
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $censortable = $xartable['censor'];
    $nextId = $dbconn->GenId($censortable);
    // Add item
    $query = "INSERT INTO $censortable (
              xar_cid,
              xar_keyword,
              xar_case_sensitive,
              xar_match_case,
              xar_locale)
            VALUES (
              ?,
              ?,
              ?,
              ?,
              ?)";
    $bindvars = array($nextId, $keyword, $case, $matchcase, serialize($locale));

    $result =& $dbconn->Execute($query,$bindvars);
    if (!$result) return;
    // Get the ID of the item that we inserted
    $cid = $dbconn->PO_Insert_ID($censortable, 'xar_cid');
    // Let any hooks know that we have created a new link
    xarModCallHooks('item', 'create', $cid, 'cid');
    // Return the id of the newly created link to the calling process
    return $cid;
}
?>

<?php
/**
 * File: $Id$
 *
 * Xaraya HTML Module
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2002 by the Xaraya Development Team.
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.org
 *
 * @subpackage HTML Module
 * @author John Cox
*/

/**
 * Create a new HTML tag
 *
 * @public
 * @author John Cox 
 * @purifiedby Richard Cave 
 * @param $args['keyword'] keyword of the item
 * @returns int
 * @return html ID on success, false on failure
 * @raise BAD_PARAM
 */
function html_adminapi_create($args)
{
    // Get arguments from argument array
    extract($args);

    // Argument check - make sure that all required arguments are present,
    // if not then set an appropriate error message and return
    if (!isset($tag) && !is_string($tag)) {
        $msg = xarML('Invalid Parameter #(1) for #(2) function #(3)() in module #(4)',
                     'tag', 'adminapi', 'create', 'html');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return;
    }

    // Security Check
	if(!xarSecurityCheck('AddHTML')) return;

    // Get datbase setup
    list($dbconn) = xarDBGetConn();
    $xartable = xarDBGetTables();

    $htmltable = $xartable['html'];

    // Get next ID in table
    $nextId = $dbconn->GenId($htmltable);

    // Add item
    $allowed = 1;
    $query = "INSERT INTO $htmltable (
              xar_cid,
              xar_tag,
              xar_allowed)
            VALUES (
              $nextId,
              '" . xarVarPrepForStore($tag) . "',
              $allowed)";
    $result =& $dbconn->Execute($query);
    if (!$result) return;

    // Get the ID of the item that we inserted
    $cid = $dbconn->PO_Insert_ID($htmltable, 'xar_cid');

    // Get the current html tags from config vars
    $allowedhtml = xarConfigGetVar('Site.Core.AllowableHTML');

    // Add the new html tag
    $allowedhtml[$tag] = $allowed;

    // Set the config vars
    xarConfigSetVar('Site.Core.AllowableHTML', $allowedhtml);

    // Let any hooks know that we have created a new html tag
    xarModCallHooks('item', 'create', $cid, 'cid');

    // Return the id of the newly created html tag to the calling process
    return $cid;
}

?>

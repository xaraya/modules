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
 * Create a new tag
 *
 * @public
 * @author John Cox 
 * @author Richard Cave 
 * @param $args['tag'] tag to create
 * @param $args['type'] type of tag to create
 * @param $args['allowed'] state of tag on creation
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
    $invalid = array();
    if (!isset($tag) || !is_string($tag)) {
        $invalid[] = 'tag';
    }
    if (!isset($type) || !is_string($type)) {
        $invalid[] = 'type';
    }
    if (!isset($allowed)) {
        // Set allowed to default 0 if not present
        $allowed = 0;
    }
    if (count($invalid) > 0) {
        $msg = xarML('Invalid Parameter #(1) for #(2) function #(3)() in module #(4)',
                     join(', ',$invalid), 'adminapi', 'create', 'html');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return;
    }

    // Security Check
	if(!xarSecurityCheck('AddHTML')) return;

    // Trim input 
    $type = trim($type);

    // Get datbase setup
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    // Set tables
    $htmltable = $xartable['html'];
    $htmltypestable = $xartable['htmltypes'];

    // Make sure type is lowercase
    $type = strtolower($type);

    // Get ID of type 
    $tagtype = xarModAPIFunc('html',
                             'user',
                             'gettype',
                             array('type' => $type));

    // Get next ID in table
    $nextId = $dbconn->GenId($htmltable);

    // Add item
    $query = "INSERT INTO $htmltable (
              xar_cid,
              xar_tid,
              xar_tag,
              xar_allowed)
            VALUES (
              $nextId,
              " . xarVarPrepForStore($tagtype['id']) . ",
              '" . xarVarPrepForStore($tag) . "',
              $allowed)";

    $result =& $dbconn->Execute($query);

    // Check for errors
    if (!$result) return;

    // Get the ID of the item that we inserted
    $cid = $dbconn->PO_Insert_ID($htmltable, 'xar_cid');

    // If this is an html tag, then
    // also add the tag to config vars
    if ($tagtype['type'] == 'html') {
        // Get the current html tags from config vars
        $allowedhtml = xarConfigGetVar('Site.Core.AllowableHTML');

        // Add the new html tag
        $allowedhtml[$tag] = $allowed;

error_log($tag . " " . $allowed);
        // Set the config vars
        xarConfigSetVar('Site.Core.AllowableHTML', $allowedhtml);
    }

    // Let any hooks know that we have created a new tag
    xarModCallHooks('item', 'create', $cid, 'cid');

    // Return the id of the newly created tag to the calling process
    return $cid;
}

?>

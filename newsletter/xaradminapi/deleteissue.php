<?php
/*
 * File: $Id: $
 *
 * Newsletter 
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003-2004 by the Xaraya Development Team
 * @link http://www.xaraya.com
 *
 * @subpackage newsletter module
 * @author Richard Cave <rcave@xaraya.com>
*/


/**
 * delete an issue
 *
 * @author Richard Cave
 * @param $args an array of arguments
 * @param $args['id'] ID of the issue
 * @returns bool
 * @return true on success, false on failure
 * @raise BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
 */
function newsletter_adminapi_deleteissue($args)
{
    // Get arguments from argument array
    extract($args);

    // Argument check
    $invalid = array();
    if (!isset($id) || !is_numeric($id)) {
        $invalid[] = 'issue id';
    }

    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    join(', ',$invalid), 'adminapi', 'deleteissue', 'Newsletter');
        xarErrorSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
        return;
    }

    // Get item
    $item = xarModAPIFunc('newsletter',
                          'user',
                          'getissue',
                          array('id' => $id));

    // Check for exceptions
    if (!isset($item) && xarCurrentErrorType() != XAR_NO_EXCEPTION) 
        return; // throw back

    // Get all the topics for this issue
    $topics = xarModAPIFunc('newsletter',
                            'user',
                            'gettopicsbyissue',
                            array('issueId' => $id));

    // Check for exceptions
    if (!isset($topics) && xarCurrentErrorType() != XAR_NO_EXCEPTION)
        return; // throw back

    // Make sure we have stories to delete
    if (!empty($topics)) {
        // Loop through topics and delete associated stories
        foreach ($topics as $topic) {
            if (!xarModAPIFunc('newsletter',
                               'admin',
                               'deletestory',
                               array('id' => $topic['storyId']))) {
                return; // throw back
            }
        }

        // Delete any topics associated with the publication
        if (!xarModAPIFunc('newsletter',
                           'admin',
                           'deletetopic',
                           array('id' => $id))) {
            return; // throw back
        }
    }

    // Get database setup
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    // Name the table and column definitions
    $nwsltrTable = $xartable['nwsltrIssues'];

    // Delete the publication
    $query = "DELETE 
                FROM $nwsltrTable
               WHERE xar_id = ?";
    $bindvars[] = (int) $id;
    $result =& $dbconn->Execute($query, $bindvars);

    // Check for an error
    if (!$result) return;

    // Let any hooks know that we have deleted an item.  As this is a
    // delete hook we're not passing any extra info
    $item['module'] = 'newsletter';
    $item['itemid'] = $id;
    xarModCallHooks('item', 'delete', $id, $item);

    // Let the calling process know that we have finished successfully
    return true;
}


?>

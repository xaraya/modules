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
 * update a topic  
 *
 * @author Richard Cave
 * @param $args an array of arguments
 * @param $args['issueId'] issue id
 * @param $args['storyId'] story id 
 * @param $args['cid'] category id of the story
 * @param $args['storyOrder'] order of the story in the issue
 * @returns int
 * @return topic ID on success, false on failure
 * @raise BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
 */
function newsletter_adminapi_updatetopic($args)
{
    // Get arguments
    extract($args);

    // Argument check
    $invalid = array();

    if (!isset($id) || !is_numeric($id)) {
        $invalid[] = 'owner ID';
    }

    // Don't check $stories as it's possible that no stories
    // were selected
    //if (!isset($stories)) {
    //    $invalid[] = 'story IDs';
    //}

    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    join(', ',$invalid), 'adminapi', 'updatetopic', 'Newsletter');
        xarErrorSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
        return;
    }

    // Delete topics for publication - this is the 
    // stories that are associated with the publication
    if (!xarModAPIFunc('newsletter',
                       'admin',
                       'deletetopic',
                       array('id' => $id))) {
        return; // throw back
    }

    // Check if no stories were selected and return - we're done
    if (empty($stories))
        return true;

    // Sort the stories
    $topics = xarModAPIFunc('newsletter',
                            'admin',
                            'sortstories',
                            array('stories' => $stories));

    if (!$topics)
        return; // throw back

    // Loop through stories and insert into table
    for ($idx=0; $idx < count($topics); ++$idx) {
        $topic = xarModAPIFunc('newsletter',
                               'admin',
                               'createtopic',
                               array('issueId' => $id,
                                     'storyId' => $topics[$idx]['storyId'],
                                     'cid' => $topics[$idx]['cid'],
                                     'storyOrder' => $idx));
        if (!$topic)
            return; // throw back
    }

    // Let the calling process know that we have finished successfully
    return true;
}

?>

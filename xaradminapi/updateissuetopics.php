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
 * update the topics for an issue
 *
 * @author Richard Cave
 * @param $args an array of arguments
 * @param $args['issueId'] issue id
 * @returns int
 * @return topic ID on success, false on failure
 * @raise BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
 */
function newsletter_adminapi_updateissuetopics($args)
{
    // Get arguments
    extract($args);

    // Argument check
    $invalid = array();

    if (!isset($issueId) || !is_numeric($issueId)) {
        $invalid[] = 'issue ID';
    }

    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    join(', ',$invalid), 'adminapi', 'updateissuetopic', 'Newsletter');
        xarErrorSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
        return;
    }
    
    // Get all the topics for this issue
    $topics = xarModAPIFunc('newsletter',
                            'user',
                            'get',
                            array('issueId' => $issueId,
                                  'phase' => 'topic'));
                                                                                      
    // Check return value
    if (!isset($topics) && xarCurrentErrorType() != XAR_NO_EXCEPTION) {
        return; // throw back
    }

    // Get all the stories
    $stories = array();
    foreach ($topics as $topic) {
        $stories[] = xarModAPIFunc('newsletter',
                                   'user',
                                   'getstory',
                                   array('id' => $topic['storyId']));

        // Check return value
        if (!isset($stories) && xarCurrentErrorType() != XAR_NO_EXCEPTION) {
            return; // throw back
        }
    }

    // Sort the stories
    $topics = xarModAPIFunc('newsletter',
                            'admin',
                            'sortstories',
                            array('stories' => $stories));

    // Check return value
    if (!$topics)
        return; // throw back

    // Delete all the topics for the issue
    if (!xarModAPIFunc('newsletter',
                       'admin',
                       'deletetopic',
                       array('id' => $issueId))) {
        return; // throw back
    }

    // Loop through stories and insert into table
    $idx = 0;
    foreach ($topics as $topic) {
        $newtopic = xarModAPIFunc('newsletter',
                                  'admin',
                                  'createtopic',
                                  array('issueId' => $issueId,
                                        'storyId' => $topic['storyId'],
                                        'cid' => $topic['cid'],
                                        'storyOrder' => $idx));
        
        // Check return value
        if (!$newtopic)
            return; // throw back

        $idx++;
    }

    // Let the calling process know that we have finished successfully
    return true;
}

?>

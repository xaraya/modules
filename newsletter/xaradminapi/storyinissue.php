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
 * Search a topic to see if an story is in the issue
 * 
 * @private
 * @author Richard Cave
 * @param $args an array of arguments
 * @param $args['issueId'] the value of the issue ID
 * @param $args['storyId'] the value of the story ID to find
 * @param $args['topics'] the array of topcis to search
 * @returns bool
 * @return $inIssue
 */
function newsletter_adminapi_storyinissue($args)
{
    // Extract args
    extract($args);

    // Argument check
    $invalid = array();
    if (!isset($issueId) || !is_numeric($issueId)) {
        $invalid[] = 'issue ID';
    }
    if (!isset($storyId) || !is_numeric($storyId)) {
        $invalid[] = 'story ID';
    }
    if (!isset($topics)) {
        $invalid[] = 'topics';
    }

    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    join(', ',$invalid), 'adminapi', 'storyinissue', 'Newsletter');
        xarErrorSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
        return;
    }

    $inIssue = false;
    for ($idx=0; $idx < count($topics); ++$idx) {
        if ($issueId == $topics[$idx]['issueId'] && $storyId == $topics[$idx]['storyId']) {
            $inIssue = true;
            break;
        }
    }
    return $inIssue;
}

?>

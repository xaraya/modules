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
 * Get the stories in a publication
 * 
 * @private
 * @author Richard Cave
 * @param $args an array of arguments
 * @param $args['display'] display 'published' or 'unpublished' stories
 * @param $args['publication']  the publication
 * @returns array
 * @return $childCategories
 */
function newsletter_userapi_getstoriesinpub($args)
{
    // Extract args
    extract($args);

    // Argument check
    $invalid = array();
    if (!isset($display) || !is_string($display)) {
        $invalid[] = 'display';
    }
    if (!isset($publication)) {
        $invalid[] = 'publication';
    }

    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    join(', ',$invalid), 'userapi', 'getstoriesinpub', 'Newsletter');
        xarErrorSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
        return;
    }

    $stories = array();

    // Get an array of stories
    $storyList = xarModAPIFunc('newsletter',
                               'user',
                               'get',
                               array('phase' => 'story',
                                     'sortby' => 'category',
                                     'display' => $display));
    
    // Check for exceptions
    if (!isset($storyList) && xarCurrentErrorType() != XAR_NO_EXCEPTION) 
        return; // throw back

    // Check if we're displaying all stories
    if ($display == 'all')
        return $storyList;

    $idx = 0;

    // Loop through stories and grab the parent category of the 
    // story's category - if the parent category matches the
    // publication category, then include in $stories array
    foreach ($storyList as $story) {
        // Get the story category
        $category = xarModAPIFunc('categories',
                                  'user',
                                  'getcatinfo', // may need to change to getcat
                                  array('cid' => $story['cid']));
                                        //'return_itself' => true,
                                        //'getparents' => false,
                                        //'getchildren' => false));

        // Check for exceptions
        if (!isset($category) && xarCurrentErrorType() != XAR_NO_EXCEPTION) 
            return;

        // Get category parent name and prepend
        $parent = xarModAPIFunc('categories',
                                'user',
                                'getcatinfo',
                                 array('cid' => $category['parent']));

        // If matches, then put in $stories array
        if (!empty($parent)) {
            if ($parent['cid'] == $publication['cid']) {
                $stories[$idx] = $story;
                $idx++;
                        
            } 
        }
    }

    return $stories;
}

?>
